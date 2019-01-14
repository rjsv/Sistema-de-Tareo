<?php

namespace App\Http\Controllers\Admin;

use App\AttendanceSetting;
use App\Currency;
use App\LeadFollowUp;
use App\Leave;
use App\Project;
use App\ProjectActivity;
use App\Task;
use App\Ticket;
use App\Traits\CurrencyExchange;
use App\UserActivity;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminDashboardController extends AdminBaseController
{
    use CurrencyExchange;

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.dashboard');
        $this->pageIcon = 'icon-speedometer';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        // Getting Attendance setting data
        $this->attendanceSettings = AttendanceSetting::first();

        //Getting Maximum Check-ins in a day
        $this->maxAttandenceInDay = $this->attendanceSettings->clockin_in_day;

        $client = new Client();
        $res = $client->request('GET', config('laraupdater.update_baseurl').'/laraupdater.json', ['verify' => false]);
        $lastVersion = $res->getBody();
        $lastVersion = json_decode($lastVersion, true);

        if ( $lastVersion['version'] > File::get('version.txt') ){
            $this->lastVersion = $lastVersion['version'];
        }

        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(users.id) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "client") as totalClients'),
                DB::raw('(select count(users.id) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee") as totalEmployees'),
                DB::raw('(select count(projects.id) from `projects`) as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid") as totalUnpaidInvoices'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs`) as totalHoursLogged'),
                DB::raw('(select count(tasks.id) from `tasks` where status="completed") as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` where status="incomplete") as totalPendingTasks'),
                DB::raw('(select count(attendances.id) from `attendances` where DATE(attendances.clock_in_time) = CURDATE()) as totalTodayAttendance'),
//                DB::raw('(select count(issues.id) from `issues` where status="pending") as totalPendingIssues'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending")) as totalUnResolvedTickets'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="resolved" or status="closed")) as totalResolvedTickets')
            )
            ->first();

        $timeLog = intdiv($this->counts->totalHoursLogged, 60).' hrs ';

        if(($this->counts->totalHoursLogged % 60) > 0){
            $timeLog.= ($this->counts->totalHoursLogged % 60).' mins';
        }

        $this->counts->totalHoursLogged = $timeLog;

        $this->pendingTasks = Task::where('status', 'incomplete')
            ->where(DB::raw('DATE(due_date)'), '<=', Carbon::today()->format('Y-m-d'))
            ->orderBy('due_date', 'desc')
            ->get();
        $this->pendingLeadFollowUps = LeadFollowUp::where(DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::today()->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where('leads.next_follow_up', 'yes')
            ->get();

        $this->newTickets = Ticket::where('status', 'open')->orderBy('id', 'desc')->get();

        $this->projectActivities = ProjectActivity::limit(15)->orderBy('id', 'desc')->get();
        $this->userActivities = UserActivity::limit(15)->orderBy('id', 'desc')->get();

        $this->feedbacks = Project::whereNotNull('feedback')->limit(5)->get();

        $locale = strtolower($this->global->locale);

        if($locale == 'pt-br' || $locale == 'vn'){
            $locale = 'es';
        }

        if(!is_null($this->global->latitude)){
            // get current weather
            $client = new Client();
            $res = $client->request('GET', 'https://api.darksky.net/forecast/9f7190aeb882036f098ba016003ab300/'.$this->global->latitude.','.$this->global->longitude.'?units=auto&exclude=minutely,daily&lang='.$locale, ['verify' => false]);
            $weather = $res->getBody();
            $this->weather = json_decode($weather, true);
        }

        // earning chart
        $this->currencies = Currency::all();
        $this->currentCurrencyId = $this->global->currency_id;

        $this->fromDate = Carbon::today()->timezone($this->global->timezone)->subDays(60);
        $this->toDate = Carbon::today()->timezone($this->global->timezone);
        $invoices = DB::table('payments')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->where('paid_on', '>=', $this->fromDate)
            ->where('paid_on', '<=', $this->toDate)
            ->where('payments.status', 'complete')
            ->groupBy('paid_on')
            ->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%Y-%m-%d") as date'),
                DB::raw('sum(amount) as total'),
                'currencies.currency_code',
                'currencies.is_cryptocurrency',
                'currencies.usd_price',
                'currencies.exchange_rate'
            ]);

        $chartData = array();
        foreach($invoices as $chart) {
            if($chart->currency_code != $this->global->currency->currency_code){
                if($chart->is_cryptocurrency == 'yes'){
                    if($chart->exchange_rate == 0){
                        if($this->updateExchangeRates()){
                            $usdTotal = ($chart->total*$chart->usd_price);
                            $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                        }
                    }
                    else{
                        $usdTotal = ($chart->total*$chart->usd_price);
                        $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                    }
                }
                else{
                    if($chart->exchange_rate == 0){
                       if($this->updateExchangeRates()){
                           $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                       }
                    }
                    else{
                        $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                    }
                }
            }
            else{
                $chartData[] = ['date' => $chart->date, 'total' => $chart->total];
            }
        }

        $this->chartData = json_encode($chartData);
        $this->leaves = Leave::where('status', '<>', 'rejected')->get();

        return view('admin.dashboard.index', $this->data);
    }
}
