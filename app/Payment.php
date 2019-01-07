<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $dates = ['paid_on'];

    protected $appends = ['total_amount', 'paid_date'];

    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function project() {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getTotalAmountAttribute(){

        if(!is_null($this->amount) && !is_null($this->currency_symbol) && !is_null($this->currency_code)){
            return $this->currency_symbol . $this->amount.' ('. $this->currency_code.')';
        }

        return "";
    }

    public function getPaidDateAttribute(){
        if(!is_null($this->paid_on)){
            return Carbon::parse($this->paid_on)->format('d F, Y');
        }
        return "";
    }


}
