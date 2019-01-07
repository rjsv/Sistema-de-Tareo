<?php

namespace App\Http\Controllers\Member;

use App\Attendance;
use App\AttendanceSetting;
use App\Helper\Reply;
use App\Holiday;
use App\Http\Requests\Attendance\StoreAttendance;
use App\Leave;
use App\ModuleSetting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MemberAttendanceController extends MemberBaseController
{

    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-clock';
        $this->pageTitle = __('app.menu.attendance');

        if(!ModuleSetting::checkModule('attendance')){
            abort(403);
        }

        // Getting Attendance setting data
        $this->attendanceSettings = AttendanceSetting::first();

        //Getting Maximum Check-ins in a day
        $this->maxAttandenceInDay = $this->attendanceSettings->clockin_in_day;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $openDays = json_decode($this->attendanceSettings->office_open_days);
        $this->startDate = Carbon::today()->timezone($this->global->timezone)->startOfMonth();
        $this->endDate = Carbon::today()->timezone($this->global->timezone);
        $this->employees = User::allEmployees();
        $this->userId = $this->user->id;

        $this->totalWorkingDays = $this->startDate->diffInDaysFiltered(function(Carbon $date) use ($openDays){
            foreach($openDays as $day){
                if($date->dayOfWeek == $day){
                    return $date;
                }
            }
        }, $this->endDate);
        $this->daysPresent = Attendance::countDaysPresentByUser($this->startDate, $this->endDate, $this->userId);
        $this->daysLate = Attendance::countDaysLateByUser($this->startDate, $this->endDate, $this->userId);
        $this->halfDays = Attendance::countHalfDaysByUser($this->startDate, $this->endDate, $this->userId);
        $this->holidays = Count(Holiday::getHolidayByDates($this->startDate->format('Y-m-d'), $this->endDate->format('Y-m-d')));
        $this->checkHoliday = Holiday::checkHolidayByDate(Carbon::now()->format('Y-m-d'));

        // Getting Current Clock-in if exist
        $this->currenntClockIn = Attendance::where(DB::raw('DATE(clock_in_time)'), Carbon::today()->format('Y-m-d'))
            ->where('user_id', $this->user->id)->whereNull('clock_out_time')->first();

        // Getting Today's Total Check-ins
        $this->todayTotalClockin = Attendance::where(DB::raw('DATE(clock_in_time)'), Carbon::today()->format('Y-m-d'))
            ->where('user_id', $this->user->id)->where(DB::raw('DATE(clock_out_time)'), Carbon::today()->format('Y-m-d'))->count();

        return view('member.attendance.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!$this->user->can('add_attendance')){
            abort(403);
        }
        return view('member.attendance.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $now = Carbon::now();

        $clockInCount = Attendance::getTotalUserClockIn($now->format('Y-m-d'), $this->user->id);

        // Check maximum attendance in a day
        if($clockInCount < $this->attendanceSettings->clockin_in_day) {

            // Set TimeZone And Convert into timestamp
            $currentTimestamp = $now->setTimezone('UTC');
            $currentTimestamp = $currentTimestamp->timestamp;;

            // Set TimeZone And Convert into timestamp in halfday time
            if($this->attendanceSettings->halfday_mark_time){
                $halfDayTimestamp = $now->format('Y-m-d') . ' ' . $this->attendanceSettings->halfday_mark_time;
                $halfDayTimestamp = Carbon::createFromFormat('Y-m-d H:i:s', $halfDayTimestamp, $this->global->timezone);
                $halfDayTimestamp = $halfDayTimestamp->setTimezone('UTC');
                $halfDayTimestamp = $halfDayTimestamp->timestamp;
            }


            $timestamp = $now->format('Y-m-d') . ' ' . $this->attendanceSettings->office_start_time;
            $officeStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $this->global->timezone);
            $officeStartTime = $officeStartTime->setTimezone('UTC');


            $lateTime = $officeStartTime->addMinutes($this->attendanceSettings->late_mark_duration);

            $checkTodayAttendance = Attendance::where('user_id', $this->user->id)
                ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $now->format('Y-m-d'))->first();

            $attendance = new Attendance();
            $attendance->user_id = $this->user->id;
            $attendance->clock_in_time = $now;
            $attendance->clock_in_ip = request()->ip();

            if (is_null($request->working_from)) {
                $attendance->working_from = 'office';
            } else {
                $attendance->working_from = $request->working_from;
            }

            if ($now->gt($lateTime) && is_null($checkTodayAttendance)) {
                $attendance->late = 'yes';
            }

            $attendance->half_day = 'no';// default halfday

            // Check day's first record and half day time
            if (!is_null($this->attendanceSettings->halfday_mark_time) && is_null($checkTodayAttendance) && $currentTimestamp > $halfDayTimestamp) {
                $attendance->half_day = 'yes';
            }

            $attendance->save();

            return Reply::successWithData(__('messages.attendanceSaveSuccess'), ['time' => $now->format('h:i A'), 'ip' => $attendance->clock_in_ip, 'working_from' => $attendance->working_from]);
        }

        return Reply::error(__('messages.maxColckIn'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $now = Carbon::now();

        $attendance = Attendance::findOrFail($id);
        $attendance->clock_out_time = $now;
        $attendance->clock_out_ip = request()->ip();
        $attendance->save();

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Attendance::destroy($id);
        return Reply::success(__('messages.attendanceDelete'));
    }

    public function refreshCount($startDate = null, $endDate = null, $userId = null){
        $openDays = json_decode($this->attendanceSettings->office_open_days);
        $startDate = Carbon::createFromFormat('!Y-m-d', $startDate);
        $endDate = Carbon::createFromFormat('!Y-m-d', $endDate)->addDay(1);

        $totalWorkingDays = $startDate->diffInDaysFiltered(function(Carbon $date) use ($openDays){
            foreach($openDays as $day){
                if($date->dayOfWeek == $day){
                    return $date;
                }
            }
        }, $endDate);
        $daysPresent = Attendance::countDaysPresentByUser($startDate, $endDate, $userId);
        $daysLate = Attendance::countDaysLateByUser($startDate, $endDate, $userId);
        $halfDays = Attendance::countHalfDaysByUser($startDate, $endDate, $userId);
        $daysAbsent = (($totalWorkingDays - $daysPresent) < 0) ? '0' : ($totalWorkingDays - $daysPresent);
        $holidays = Count(Holiday::getHolidayByDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d')));

        return Reply::dataOnly(['daysPresent' => $daysPresent, 'daysLate' => $daysLate, 'halfDays' => $halfDays, 'totalWorkingDays' => $totalWorkingDays, 'absentDays' => $daysAbsent, 'holidays' => $holidays]);

    }

    public function employeeData($startDate = null, $endDate = null, $userId = null){

        $ant = [];
        $dateWiseData = [];

        $attendances = Attendance::userAttendanceByDate($startDate, $endDate, $userId);// Getting Attendance Data
        $holidays = Holiday::getHolidayByDates($startDate, $endDate); // Getting Holiday Data

        // Getting Leaves Data
        $leavesDates = Leave::where('user_id', $userId)
            ->where('leave_date', '>=', $startDate)
            ->where('leave_date', '<=', $endDate)
            ->where('status', 'approved')
            ->select('leave_date', 'reason')
            ->get()->keyBy('date')->toArray();

        $holidayData = $holidays->keyBy('date');
        $holidayArray = $holidayData->toArray();

        // Set Date as index for same date clock-ins
        foreach($attendances as $attand){
            $ant[$attand->clock_in_date][] = $attand;
        }

        $endDate = Carbon::parse($endDate);
        $startDate = Carbon::parse($startDate)->subDay();

        // Set All Data in a single Array
        for($date = $endDate; $date->diffInDays($startDate) > 0; $date->subDay()){

            // Set default array for record
            $dateWiseData[$date->toDateString()] = [
                'holiday' =>false,
                'attendance' =>false,
                'leave' =>false
            ];

            // Set Holiday Data
            if(array_key_exists($date->toDateString(), $holidayArray)){
                $dateWiseData[$date->toDateString()]['holiday'] = $holidayData[$date->toDateString()];
            }

            // Set Attendance Data
            if(array_key_exists($date->toDateString(), $ant)){
                $dateWiseData[$date->toDateString()]['attendance'] = $ant[$date->toDateString()];
            }

            // Set Leave Data
            if(array_key_exists($date->toDateString(), $leavesDates)){
                $dateWiseData[$date->toDateString()]['leave'] = $leavesDates[$date->toDateString()];
            }

        }

        // Getting View data
        $view = view('member.attendance.user_attendance',['dateWiseData' => $dateWiseData,'global' => $this->global])->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    public function data(Request $request){

        $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
        $attendances = Attendance::attendanceByDate($date);

        return DataTables::of($attendances)
            ->editColumn('id', function ($row) {
                return view('member.attendance.attendance_list', ['row' => $row, 'global' => $this->global, 'maxAttandenceInDay' => $this->maxAttandenceInDay])->render();
            })
            ->rawColumns(['id'])
            ->removeColumn('name')
            ->removeColumn('clock_in_time')
            ->removeColumn('clock_out_time')
            ->removeColumn('image')
            ->removeColumn('attendance_id')
            ->removeColumn('working_from')
            ->removeColumn('late')
            ->removeColumn('half_day')
            ->removeColumn('clock_in_ip')
            ->removeColumn('job_title')
            ->removeColumn('clock_in')
            ->removeColumn('total_clock_in')

            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAttendance(StoreAttendance $request)
    {
        $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('h:i A', $request->clock_in_time, $this->global->timezone);
        $clockIn->setTimezone('UTC');
        $clockIn = $clockIn->format('H:i:s');
        if($request->clock_out_time != ''){
            $clockOut = Carbon::createFromFormat('h:i A', $request->clock_out_time, $this->global->timezone);
            $clockOut->setTimezone('UTC');
            $clockOut = $clockOut->format('H:i:s');
            $clockOut = $date.' '.$clockOut;
        }
        else{
            $clockOut = null;
        }

        $clockInCount = Attendance::getTotalUserClockIn($date, $request->user_id);

        $attendance = Attendance::where('user_id', $request->user_id)
            ->where(DB::raw('DATE(`clock_in_time`)'), $date)
            ->whereNull('clock_out_time')
            ->first();

        if(!is_null($attendance)){
            $attendance->update([
                'user_id' => $request->user_id,
                'clock_in_time' => $date.' '.$clockIn,
                'clock_in_ip' => $request->clock_in_ip,
                'clock_out_time' => $clockOut,
                'clock_out_ip' => $request->clock_out_ip,
                'working_from' => $request->working_from,
                'late' => $request->late,
                'half_day' => $request->half_day
            ]);
        }else{

            // Check maximum attendance in a day
            if($clockInCount < $this->attendanceSettings->clockin_in_day)
            {
                Attendance::create([
                    'user_id' => $request->user_id,
                    'clock_in_time' => $date.' '.$clockIn,
                    'clock_in_ip' => $request->clock_in_ip,
                    'clock_out_time' => $clockOut,
                    'clock_out_ip' => $request->clock_out_ip,
                    'working_from' => $request->working_from,
                    'late' => $request->late,
                    'half_day' => $request->half_day
                ]);
            }
            else{
                return Reply::error(__('messages.maxColckIn'));
            }
        }

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    public function checkHoliday(Request $request){
        $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
        $checkHoliday = Holiday::checkHolidayByDate($date);
        return Reply::dataOnly(['status' => 'success', 'holiday' => $checkHoliday]);
    }

    // Attendance Detail Show
    public function attendanceDetail(Request $request){

        // Getting Attendance Data By User And Date
        $this->attendances =  Attendance::attedanceByUserAndDate($request->date, $request->userID);
        return view('member.attendance.attendance-detail', $this->data)->render();
    }
}
