<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModuleSetting extends Model
{

    protected $guarded = ['id'];

    public static function checkModule($moduleName) {
        $module = ModuleSetting::where('module_name', $moduleName)
            ->first();
        if($module->status == 'active'){
            return true;
        }
        return false;
    }
}
