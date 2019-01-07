<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\LeaveType;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeavesSettingController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.leaveSettings');
        $this->pageIcon = 'ti-settings';
    }

    public function index(){
        $this->leaveTypes = LeaveType::all();
        return view('admin.leaves-settings.index', $this->data);
    }

    public function store(Request $request){
        $setting = Setting::first();
        $setting->leaves_start_from = $request->leaveCountFrom;
        $setting->save();

        return Reply::success(__('messages.settingsUpdated'));
    }
}
