<?php

namespace App\Http\Controllers\Member;

use App\EmployeeDetails;
use App\Helper\Reply;
use App\Http\Requests\ProjectMembers\StoreProjectMembers;
use App\Http\Requests\Tasks\StoreTask;
use App\Http\Requests\User\UpdateProfile;
use App\Issue;
use App\ModuleSetting;
use App\Notifications\NewTask;
use App\Notifications\TaskCompleted;
use App\Notifications\TaskUpdated;
use App\Project;
use App\ProjectActivity;
use App\ProjectMember;
use App\ProjectTimeLog;
use App\SubTask;
use App\Task;
use App\Traits\ProjectProgress;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class MemberProjectsController
 * @package App\Http\Controllers\Member
 */
class MemberTasksController extends MemberBaseController
{
    use ProjectProgress;

    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = __('app.menu.projects');

        if(!ModuleSetting::checkModule('tasks')){
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTask $request)
    {
        $task = new Task();
        $task->heading = $request->heading;
        if($request->description != ''){
            $task->description = $request->description;
        }
        $task->due_date = Carbon::parse($request->due_date)->format('Y-m-d');
        $task->user_id = $request->user_id;
        $task->project_id = $request->project_id;
        $task->priority = $request->priority;
        $task->status = 'incomplete';
        $task->save();

//      Send notification to user
        $notifyUser = User::withoutGlobalScope('active')->findOrFail($request->user_id);
        $notifyUser->notify(new NewTask($task));

        $this->logProjectActivity($request->project_id, __('messages.newTaskAddedToTheProject'));

        $this->project = Project::findOrFail($task->project_id);
        $view = view('admin.projects.tasks.task-list-ajax', $this->data)->render();

        //calculate project progress if enabled
        $this->calculateProjectProgress($request->project_id);

        //log search
        $this->logSearchEntry($task->id, 'Task: '.$task->heading, 'admin.all-tasks.edit');

        return Reply::successWithData(__('messages.taskCreatedSuccessfully'), ['html' => $view]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->project = Project::findOrFail($id);
        if($this->project->isProjectAdmin || $this->user->can('edit_projects'))
            $this->tasks = Task::where('project_id', $id)->get();
        else
            $this->tasks = Task::where('project_id', $id)->where('user_id', $this->user->id)->get();

        return view('member.tasks.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->task = Task::findOrFail($id);
        $view = view('member.tasks.edit', $this->data)->render();
        return Reply::dataOnly(['html' => $view]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTask $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->heading = $request->heading;
        if($request->description != ''){
            $task->description = $request->description;
        }
        $task->due_date = Carbon::parse($request->due_date)->format('Y-m-d');
        $task->user_id = $request->user_id;
        $task->priority = $request->priority;
        $task->status = $request->status;

        if($task->status == 'completed'){
            $task->completed_on = Carbon::now();
        }else{
            $task->completed_on = null;
        }

        $task->save();

        //Send notification to user
        $notifyUser = User::findOrFail($request->user_id);
        $notifyUser->notify(new TaskUpdated($task));

        //calculate project progress if enabled
        $this->calculateProjectProgress($request->project_id);

        $this->project = Project::findOrFail($task->project_id);

        $view = view('admin.projects.tasks.task-list-ajax', $this->data)->render();

        return Reply::successWithData(__('messages.taskUpdatedSuccessfully'), ['html' => $view]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function changeStatus(Request $request)
    {
        $taskId = $request->taskId;
        $status = $request->status;

        $task = Task::findOrFail($taskId);

        if(auth()->user()->id == $task->user_id || $task->project->isProjectAdmin || $this->user->can('edit_tasks'))
        {
            $task->status = $status;

            if($task->status == 'completed'){
                $task->completed_on = Carbon::now();

                // send task complete notification
                $notifyUser = User::withoutGlobalScope('active')->findOrFail($task->user_id);
                $notifyUser->notify(new TaskCompleted($task));

                $admins = User::allAdmins($task->user_id);

                Notification::send($admins, new TaskCompleted($task));
            }else{
                $task->completed_on = null;
            }

            $task->save();

            //calculate project progress if enabled
            $this->calculateProjectProgress($task->project_id);

            $this->project = Project::findOrFail($task->project_id);

            if($this->project->isProjectAdmin || $this->user->can('edit_tasks'))
                $this->project->tasks = Task::where('project_id', $this->project->id)->orderBy($request->sortBy, 'desc')->get();
            else
                $this->project->tasks = Task::where('project_id', $this->project->id)->orderBy($request->sortBy, 'desc')->where('user_id', $this->user->id)->get();


            $view = view('member.tasks.task-list-ajax', $this->data)->render();

            $this->logUserActivity($this->user->id, __('messages.taskUpdated').'<i>'.strtolower($task->status).'</i> : <strong>'.ucfirst($task->heading).'</strong>');

            return Reply::successWithData(__('messages.taskUpdatedSuccessfully'), ['html' => $view]);

        }else{
            return Reply::error(Lang::get('messages.unAuthorisedUser'));
        }

    }

    public function sort(Request $request) {
        $projectId = $request->projectId;
        $this->sortBy = $request->sortBy;

        $this->project = Project::findOrFail($projectId);
        if($request->sortBy == 'due_date'){
            $order = "asc";
        }
        else{
            $order = "desc";
        }

        if($this->project->isProjectAdmin){
            $tasks = Task::whereProjectId($projectId)
                ->orderBy($request->sortBy, $order);
        }
        else{
            $tasks = Task::whereProjectId($projectId)
                ->where('user_id', $this->user->id)
                ->orderBy($request->sortBy, $order);
        }

        if($request->hideCompleted == '1'){
            $tasks->where('status', 'incomplete');
        }

        $this->project->tasks = $tasks->get();

        $view = view('member.tasks.task-list-ajax', $this->data)->render();

        return Reply::successWithData('', ['html' => $view]);
    }

    public function checkTask($taskID){
        $task = Task::findOrFail($taskID);
        $subTask = SubTask::where('task_id', $taskID)->count();

        return Reply::dataOnly(['taskCount' => $subTask, 'lastStatus' => $task->status]);
    }

}
