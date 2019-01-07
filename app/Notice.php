<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notice extends Model
{
    use Notifiable;
    protected $appends = ['notice_date'];

    public function getNoticeDateAttribute(){
        if(!is_null($this->created_at)){
            return Carbon::parse($this->created_at)->format('d F, Y');
        }
        return "";
    }
}
