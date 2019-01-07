<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskboardColumn extends Model
{
    public function tasks(){
        return $this->hasMany(Task::class, 'board_column_id')->orderBy('column_priority');
    }

    public function membertasks(){
        return $this->hasMany(Task::class, 'board_column_id')->where('user_id', auth()->user()->id)->orderBy('column_priority');
    }
}
