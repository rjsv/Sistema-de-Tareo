<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Task extends Model
{
    use Notifiable;

    public function routeNotificationForMail()
    {
        return $this->user->email;
    }

    protected $dates = ['due_date', 'completed_on', 'start_date'];
    protected $appends = ['due_on','create_on'];

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }
    public function category(){
        return $this->belongsTo(TaskCategory::class,'task_category_id');
    }

    public function subtasks(){
        return $this->hasMany(SubTask::class, 'task_id');
    }

    public function comments(){
        return $this->hasMany(TaskComment::class, 'task_id')->orderBy('id', 'desc');
    }


    /**
     * @return string
     */
    public function getDueOnAttribute(){
        if(!is_null($this->due_date)){
            return $this->due_date->format('d M, y');
        }
        return "";
    }
    public function getCreateOnAttribute(){
        if(!is_null($this->start_date)){
            return $this->start_date->format('d M, y');
        }
        return "";
    }

    /**
     * @param $projectId
     * @param null $userID
     */
    public static function projectOpenTasks($projectId, $userID=null)
    {
        $projectTask = Task::where('status', 'incomplete');

        if($userID)
        {
            $projectIssue = $projectTask->where('user_id', '=', $userID);
        }

        $projectIssue = $projectTask->where('project_id', $projectId)
            ->get();

        return $projectIssue;
    }

    public static function projectCompletedTasks($projectId)
    {
        return Task::where('status', 'completed')
            ->where('project_id', $projectId)
            ->get();
    }

}
