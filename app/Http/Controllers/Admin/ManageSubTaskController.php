<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\SubTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManageSubTaskController extends AdminBaseController
{

    public function __construct() {
        parent::__construct();
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
    public function store(Request $request)
    {
        $subTask = new SubTask();
        $name = $request->name;
        $value = $request->value;
        $subTask->$name = $value;
        $subTask->task_id = $request->task_id;
        $subTask->save();

        $this->subTasks = SubTask::where('task_id', $request->task_id)->get();
        $view = view('admin.sub_task.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $subTask = SubTask::findOrFail($id);
        $name = $request->name;
        $value = $request->value;
        $subTask->$name = $value;
        $subTask->task_id = $request->task_id;
        $subTask->save();

        $this->subTasks = SubTask::where('task_id', $request->task_id)->get();
        $view = view('admin.sub_task.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subTask = SubTask::findOrFail($id);
        SubTask::destroy($id);

        $this->subTasks = SubTask::where('task_id', $subTask->task_id)->get();
        $view = view('admin.sub_task.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function changeStatus(Request $request){
        $subTask = SubTask::findOrFail($request->subTaskId);
        $subTask->status = $request->status;
        $subTask->save();

        $this->subTasks = SubTask::where('task_id', $subTask->task_id)->get();
        $view = view('admin.sub_task.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
}
