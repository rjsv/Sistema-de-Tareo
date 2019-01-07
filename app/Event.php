<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $dates = ['start_date_time', 'end_date_time'];

    public function attendee(){
        return $this->hasMany(EventAttendee::class, 'event_id');
    }
}
