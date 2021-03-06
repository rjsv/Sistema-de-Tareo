<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }
}
