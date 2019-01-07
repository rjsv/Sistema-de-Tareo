<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\TaskBoard\StoreTaskBoard;
use App\ModuleSetting;
use App\Task;
use App\TaskboardColumn;
use Illuminate\Http\Request;


class AdminTaskboardController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('modules.tasks.taskBoard');
        $this->pageIcon = 'ti-layout-column3';

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
        $this->boardColumns = TaskboardColumn::orderBy('priority', 'asc')->get();
        return view('admin.taskboard.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.taskboard.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskBoard $request)
    {
        $maxPriority = TaskboardColumn::max('priority');

        $board = new TaskboardColumn();
        $board->column_name = $request->column_name;
        $board->label_color = $request->label_color;
        $board->priority = ($maxPriority+1);
        $board->save();

        return Reply::redirect(route('admin.taskboard.index'), __('messages.boardColumnSaved'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->boardColumn = TaskboardColumn::findOrFail($id);
        $this->maxPriority = TaskboardColumn::max('priority');
        $view =  view('admin.taskboard.edit', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTaskBoard $request, $id)
    {
        $board = TaskboardColumn::findOrFail($id);
        $oldPosition = $board->priority;
        $newPosition = $request->priority;

        if($oldPosition < $newPosition){

            $otherColumns = TaskboardColumn::where('priority', '>', $oldPosition)
                ->where('priority', '<=', $newPosition)
                ->orderBy('priority', 'asc')
                ->get();

            foreach($otherColumns as $column){
                $pos = TaskboardColumn::where('priority', $column->priority)->first();
                $pos->priority = ($pos->priority-1);
                $pos->save();
            }
        }
        else if($oldPosition > $newPosition){

            $otherColumns = TaskboardColumn::where('priority', '<', $oldPosition)
                ->where('priority', '>=', $newPosition)
                ->orderBy('priority', 'asc')
                ->get();

            foreach($otherColumns as $column){
                $pos = TaskboardColumn::where('priority', $column->priority)->first();
                $pos->priority = ($pos->priority+1);
                $pos->save();
            }
        }

        $board->column_name = $request->column_name;
        $board->label_color = $request->label_color;
        $board->priority = $request->priority;
        $board->save();

        return Reply::redirect(route('admin.taskboard.index'), __('messages.boardColumnSaved'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Task::where('board_column_id', $id)->update(['board_column_id' => 1]);

        $board = TaskboardColumn::findOrFail($id);

        $otherColumns = TaskboardColumn::where('priority', '>', $board->priority)
            ->orderBy('priority', 'asc')
            ->get();

        foreach($otherColumns as $column){
            $pos = TaskboardColumn::where('priority', $column->priority)->first();
            $pos->priority = ($pos->priority-1);
            $pos->save();
        }

        TaskboardColumn::destroy($id);

        return Reply::dataOnly(['status' => 'success']);
    }

    public function updateIndex(Request $request){
        $taskIds = $request->taskIds;
        $boardColumnIds = $request->boardColumnIds;
        $priorities = $request->prioritys;

        if(isset($taskIds) && count($taskIds) > 0){

            $taskIds = (array_filter($taskIds, function($value) { return $value !== null; }));

            foreach($taskIds as $key=>$taskId){
                if(!is_null($taskId)){
                    //update status of task if column is incomplete or completed
                    $board = TaskboardColumn::findOrFail($boardColumnIds[$key]);

                    $task = Task::findOrFail($taskId);
                    $task->board_column_id = $boardColumnIds[$key];
                    $task->column_priority = $priorities[$key];

                    if($board->priority == '1'){
                        $task->status = 'incomplete';
                    }
                    elseif($board->priority == '2') {
                        $task->status = 'completed';
                    }

                    $task->save();
                }
            }
        }

        return Reply::dataOnly(['status' => 'success']);
    }
}
