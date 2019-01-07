<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'organisation_settings';

    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
