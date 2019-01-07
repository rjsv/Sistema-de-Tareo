<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Leave extends Model
{
    protected $dates = ['leave_date'];
    protected $appends = ['date'];


    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function type(){
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
    public function getDateAttribute()
    {
        return $this->leave_date->toDateString();
    }

    public static function byUser($userId){
        $setting = Setting::first();
        $user = User::withoutGlobalScope('active')->findOrFail($userId);

        if($setting->leaves_start_from == 'joining_date') {
            return Leave::where('user_id', $userId)
                ->where('leave_date','<=', $user->employee[0]->joining_date->format((Carbon::now()->year+1).'-m-d'))
                ->where('status', 'approved')
                ->get();

        } else{
            return Leave::where('user_id', $userId)
                ->where('leave_date','<=', Carbon::today()->endOfYear()->format('Y-m-d'))
                ->where('status', 'approved')
                ->get();
        }
    }
}
