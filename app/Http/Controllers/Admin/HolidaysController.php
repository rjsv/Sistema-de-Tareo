<?php
namespace App\Http\Controllers\Admin;

use App\AttendanceSetting;
use App\Helper\Reply;
use App\Http\Requests\CommonRequest;
use App\Http\Requests\Holiday\CreateRequest;
use App\Http\Requests\Holiday\DeleteRequest;
use App\Http\Requests\Holiday\IndexRequest;
use App\Http\Requests\Holiday\UpdateRequest;
use App\Holiday;
use App\ModuleSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class HolidaysController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'user-follow';
        $this->pageTitle = 'Holiday';

        if(!ModuleSetting::checkModule('holidays')){
            abort(403);
        }

        for ($m = 1; $m <= 12; $m++) {
            $month[] = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
        }

        $this->months = $month;
        $this->currentMonth = date('F');
    }

    public function index(IndexRequest $request)
    {
        $this->holidays = Holiday::orderBy('date', 'ASC')->get();;
        $this->holidayActive = 'active';
        $hol = [];

        $year = Carbon::now()->format('Y');
        $dateArr = $this->getDateForSpecificDayBetweenDates($year . '-01-01', $year . '-12-31', 0);
        $this->number_of_sundays = count($dateArr);

        $this->holidays_in_db = count($this->holidays);

        foreach ($this->holidays as $holiday) {
            $hol[date('F', strtotime($holiday->date))]['id'][] = $holiday->id;
            $hol[date('F', strtotime($holiday->date))]['date'][] = date('d F Y', strtotime($holiday->date));
            $hol[date('F', strtotime($holiday->date))]['ocassion'][] = ($holiday->occassion)? $holiday->occassion : 'Not Define'; ;
            $hol[date('F', strtotime($holiday->date))]['day'][] = date('D', strtotime($holiday->date));
        }
        $this->holidaysArray = $hol;
        return View::make('admin.holidays.index', $this->data);
    }

    /**
     * Show the form for creating a new holiday
     *
     * @return Response
     */
    public function create()
    {
        return View::make('admin.holidays.create');
    }

    /**
     * Store a newly created holiday in storage.
     *
     * @return Response
     */
    public function store(CreateRequest $request)
    {
        $holiday = array_combine($request->date, $request->occasion);
        foreach ($holiday as $index => $value) {
            if ($index){
                $add = Holiday::firstOrCreate([
                'date' => Carbon::createFromFormat('d/m/Y', $index)->format('Y-m-d'),
                'occassion' => $value,
                ]);
            }
        }
        return Reply::redirect(route('admin.holidays.index'), __('messages.holidayAddedSuccess'));
    }

    /**
     * Display the specified holiday.
     */
    public function show($id)
    {
        $holiday = Holiday::findOrFail($id);

        return View::make('admin.holidays.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified holiday.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $holiday = Holiday::find($id);

        return View::make('admin.holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified holiday in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $holiday = Holiday::findOrFail($id);
        $data = Input::all();
        $holiday->update($data);

        return Redirect::route('admin.holidays.index');
    }

    /**
     * Remove the specified holiday from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(DeleteRequest $request, $id)
    {
        Holiday::destroy($id);
        return Reply::redirect(route('admin.holidays.index'), __('messages.holidayDeletedSuccess'));
    }

    /**
     * @return array
     */

    public function Sunday()
    {
        $year = Carbon::now()->format('Y');

        $dateArr = $this->getDateForSpecificDayBetweenDates($year . '-01-01', $year . '-12-31', 0);

        foreach ($dateArr as $date) {
            Holiday::firstOrCreate([
                'date' => $date,
                'occassion' => 'Sunday'
            ]);
        }
        return Reply::redirect(route('admin.holidays.index'), '<strong>All Sundays</strong> successfully added to the Database');
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $weekdayNumber
     * @return array
     */
    public function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        $dateArr = [];

        do {
            if (date('w', $startDate) != $weekdayNumber) {
                $startDate += (24 * 3600); // add 1 day
            }
        } while (date('w', $startDate) != $weekdayNumber);


        while ($startDate <= $endDate) {
            $dateArr[] = date('Y-m-d', $startDate);
            $startDate += (7 * 24 * 3600); // add 7 days
        }

        return ($dateArr);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function holidayCalendar(){
        $this->pageTitle = 'Holiday Calendar';

        $this->holidays = Holiday::all();
        return view('admin.holidays.holiday-calendar', $this->data);
    }

    public function markHoliday()
    {
        $this->days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];

        $attandanceSetting = AttendanceSetting::first();

        $this->holidays = $this->missing_number(json_decode($attandanceSetting->office_open_days));
        $holidaysArray = [];
        foreach($this->holidays as $index => $holiday){
            $holidaysArray[$holiday] = $this->days[$holiday-1];
        }
        $this->holidaysArray = $holidaysArray;

        return View::make('admin.holidays.mark-holiday', $this->data);
    }

    public function missing_number($num_list)
    {
        // construct a new array
        $new_arr = range(1,7);
        return array_diff($new_arr, $num_list);
    }

    public function markDayHoliday(CommonRequest $request){

        if (!$request->has('office_holiday_days')) {
            return Reply::error(__('messages.checkDayHoliday'));
        }

        $daysss = [];
        $this->days = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];

        if($request->office_holiday_days != null && count($request->office_holiday_days) > 0){
            foreach($request->office_holiday_days as $holiday){
                $year = Carbon::now()->format('Y');
//                dd($this->days[($holiday-1)], $holiday);
                $daysss[] = $this->days[($holiday-1)];
                $day = $holiday;
                if($holiday == 7){
                    $day = 0;
                }
                $dateArr = $this->getDateForSpecificDayBetweenDates($year . '-01-01', $year . '-12-31', ($day));

                foreach ($dateArr as $date) {
                    Holiday::firstOrCreate([
                        'date' => $date,
                        'occassion' => $this->days[$day]
                    ]);
                }
            }

        }
        return Reply::redirect(route('admin.holidays.index'), '<strong>All Sundays</strong> successfully added to the Database');
    }
}
