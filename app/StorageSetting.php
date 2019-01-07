<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageSetting extends Model
{
    protected $table = 'file_storage_settings';

    protected $fillable = ['filesystem','auth_keys','status'];
}
