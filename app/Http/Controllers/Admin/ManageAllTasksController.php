<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\Tasks\StoreTask;
use App\ModuleSetting;
use App\Notifications\NewClientTask;
use App\Notifications\NewTask;
use App\Notifications\TaskCompleted;
use App\Notifications\TaskUpdated;
use App\Notifications\TaskUpdatedClient;
use App\Project;
use App\ProjectMember;
use App\Task;
use App\TaskCategory;
use App\Traits\ProjectProgress;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManageAllTasksController extends AdminBaseController
{
    use ProjectProgress;

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.tasks');
        $this->pageIcon = 'ti-layout-list-thumb';

        if(!ModuleSetting::checkModule('tasks')){
            abort(403);
        }
    }

    public function index() {
        $this->projects = Project::all();


        return view('admin.tasks.index', $this->data);
    }

    public function data($startDate = null, $endDate = null, $hideCompleted = null, $projectId = null) {
        $tasks = Task::leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->join('users', 'users.id', '=', 'tasks.user_id')
            ->select('tasks.id', 'projects.project_name', 'tasks.heading', 'users.name', 'users.image', 'tasks.due_date', 'tasks.status', 'tasks.project_id');

        if(!is_null($startDate)){
            $tasks->where(DB::raw('DATE(tasks.`due_date`)'), '>=', $startDate);
        }

        if(!is_null($endDate)){
            $tasks->where(DB::raw('DATE(tasks.`due_date`)'), '<=', $endDate);
        }

        if($projectId != 0){
            $tasks->where('tasks.project_id', '=', $projectId);
        }

        if($hideCompleted == '1'){
            $tasks->where('tasks.status', '=', 'incomplete');
        }

        $tasks->get();

        return DataTables::of($tasks)
            ->addColumn('action', function($row){
                return '<a href="'.route('admin.all-tasks.edit', $row->id).'" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        &nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-task-id="'.$row->id.'" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn('due_date', function($row){
                if($row->due_date->isPast()) {
                    return '<span class="text-danger">'.$row->due_date->format('d M, y').'</span>';
                }
                return '<span class="text-success">'.$row->due_date->format('d M, y').'</span>';
            })
            ->editColumn('name', function($row){
                return ($row->image) ? '<img src="'.asset('user-uploads/avatar/'.$row->image).'"
                                                            alt="user" class="img-circle" width="30"> '.ucwords($row->name) : '<img src="'.asset('default-profile-2.png').'"
                                                            alt="user" class="img-circle" width="30"> '.ucwords($row->name);
            })
            ->editColumn('heading', function($row){
                return '<a href="javascript:;" data-task-id="'.$row->id.'" class="show-task-detail">'.ucfirst($row->heading).'</a>';
            })
            ->editColumn('status', function($row){
                if($row->status == 'incomplete'){
                    return '<label class="label label-danger">'.__('app.incomplete').'</label>';
                }
                return '<label class="label label-success">'.__('app.completed').'</label>';
            })
            ->editColumn('project_name', function ($row) {
                if(is_null($row->project_id)){
                    return "";
                }
                return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project_name) . '</a>';
            })
            ->rawColumns(['status', 'action', 'project_name', 'due_date', 'name', 'heading'])
            ->removeColumn('project_id')
            ->removeColumn('image')
            ->make(true);
    }

    public function edit($id) {
        $this->task = Task::findOrFail($id);
        $this->projects = Project::all();
        $this->employees = User::allEmployees();
        $this->categories = TaskCategory::all();

        return view('admin.tasks.edit', $this->data);
    }

    public function update(StoreTask $request, $id)
    {
        $task = Task::findOrFail($id);
        $oldStatus = $task->status;
        $task->heading = $request->heading;
        if($request->description != ''){
            $task->description = $request->description;
        }
        $task->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $task->due_date = Carbon::parse($request->due_date)->format('Y-m-d');
        $task->user_id = $request->user_id;
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->status = $request->status;

        if($task->status == 'completed'){
            $task->completed_on = Carbon::today()->format('Y-m-d');
        }else{
            $task->completed_on = null;
        }

        $task->project_id = $request->project_id;
        $task->save();

        if($oldStatus == 'incomplete'  && $task->status == 'completed'){
            // notify user
            $notifyUser = User::withoutGlobalScope('active')->findOrFail($request->user_id);
            $notifyUser->notify(new TaskCompleted($task));

            if($task->project_id != null){
                if($task->project->client_id != null) {
                    $notifyClient = User::findOrFail($task->project->client_id);
                    $notifyClient->notify(new TaskCompleted($task));
                }
            }
        }else{
            //Send notification to user
            $notifyUser = User::findOrFail($request->user_id);
            $notifyUser->notify(new TaskUpdated($task));

            if($task->project_id != null){
                if($task->project->client_id != null && $task->project->allow_client_notification == 'enable') {
                    $notifyUser = User::withoutGlobalScope('active')->findOrFail($task->project->client_id);
                    $notifyUser->notify(new TaskUpdatedClient($task));
                }
            }
        }

        //calculate project progress if enabled
        $this->calculateProjectProgress($request->project_id);

        return Reply::success(__('messages.taskUpdatedSuccessfully'));
    }

    public function destroy($id) {
        $task = Task::findOrFail($id);
        Task::destroy($id);

        //calculate project progress if enabled
        $this->calculateProjectProgress($task->project_id);

        return Reply::success(__('messages.taskDeletedSuccessfully'));
    }

    public function create() {
        $this->projects = Project::all();
        $this->employees = User::allEmployees();
        $this->categories = TaskCategory::all();
        return view('admin.tasks.create', $this->data);
    }

    public function membersList($projectId){
        $this->members = ProjectMember::byProject($projectId);
        $list = view('admin.tasks.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    public function store(StoreTask $request) {
        $task = new Task();
        $task->heading = $request->heading;
        if($request->description != ''){
            $task->description = $request->description;
        }
        $task->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $task->due_date = Carbon::parse($request->due_date)->format('Y-m-d');
        $task->user_id = $request->user_id;
        $task->project_id = $request->project_id;
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->status = 'incomplete';

        if($request->board_column_id){
            $task->board_column_id = $request->board_column_id;
        }
        $task->save();

        //calculate project progress if enabled
        $this->calculateProjectProgress($request->project_id);

//      Send notification to user
        $notifyUser = User::withoutGlobalScope('active')->findOrFail($request->user_id);
        $notifyUser->notify(new NewTask($task));

        if($task->project_id != null){
            if($task->project->client_id != null && $task->project->allow_client_notification == 'enable') {
                $notifyUser = User::withoutGlobalScope('active')->findOrFail($task->project->client_id);
                $notifyUser->notify(new NewClientTask($task));
            }
        }

        if(!is_null($request->project_id)){
            $this->logProjectActivity($request->project_id, __('messages.newTaskAddedToTheProject'));
        }

        //log search
        $this->logSearchEntry($task->id, 'Task '.$task->heading, 'admin.all-tasks.edit');

        if($request->board_column_id){
            return Reply::redirect(route('admin.taskboard.index'), __('messages.taskCreatedSuccessfully'));
        }
        return Reply::redirect(route('admin.all-tasks.index'), __('messages.taskCreatedSuccessfully'));
    }

    public function ajaxCreate($columnId){
        $this->projects = Project::all();
        $this->columnId = $columnId;
        $this->employees = User::allEmployees();
        return view('admin.tasks.ajax_create', $this->data);
    }

    public function show($id){
        $this->task = Task::findOrFail($id);
        $view = view('admin.tasks.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $projectId
     * @param $hideCompleted
     */
    public function export($startDate, $endDate, $projectId, $hideCompleted) {

        $tasks = Task::leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->join('users', 'users.id', '=', 'tasks.user_id')
            ->select('tasks.id', 'projects.project_name', 'tasks.heading', 'users.name', 'users.image', 'tasks.status', 'tasks.due_date');

        if(!is_null($startDate)){
            $tasks->where(DB::raw('DATE(tasks.`due_date`)'), '>=', $startDate);
        }

        if(!is_null($endDate)){
            $tasks->where(DB::raw('DATE(tasks.`due_date`)'), '<=', $endDate);
        }

        if($projectId != 0){
            $tasks->where('tasks.project_id', '=', $projectId);
        }

        if($hideCompleted == '1'){
            $tasks->where('tasks.status', '=', 'incomplete');
        }

        $attributes =  ['image', 'due_date'];

        $tasks = $tasks->get()->makeHidden($attributes);

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Project','Title','Assigned TO', 'Status','Due Date'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($tasks as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('task', function($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Task');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('task file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));

                });

            });

        })->download('xlsx');
    }


}
