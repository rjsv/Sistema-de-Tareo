<?php

namespace App;

use App\Traits\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Model;

class ClientDetails extends Model
{
    use CustomFieldsTrait;

    protected $table = 'client_details';

    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }
}
