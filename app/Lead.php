<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'leads';

    public function lead_source(){
        return $this->belongsTo(LeadSource::class, 'source_id');
    }
    public function lead_status(){
        return $this->belongsTo(LeadStatus::class, 'status_id');
    }
    public function follow() {
        return $this->hasMany(LeadFollowUp::class);
    }
    public function files() {
        return $this->hasMany(LeadFiles::class);
    }
}
