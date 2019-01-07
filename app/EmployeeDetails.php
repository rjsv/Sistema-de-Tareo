<?php

namespace App;

use App\Traits\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetails extends Model
{
    use CustomFieldsTrait;

    protected $table = 'employee_details';

    protected $dates = ['joining_date'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }
}
