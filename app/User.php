<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Zizaco\Entrust\Entrust;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', '=', 'active');
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $dates = ['created_at', 'updated_at'];

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        $slack = SlackSetting::first();
        return $slack->slack_webhook;
    }


    public function client()
    {
        return $this->hasMany(ClientDetails::class, 'user_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function employee()
    {
        return $this->hasMany(EmployeeDetails::class, 'user_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function member() {
        return $this->hasMany(ProjectMember::class, 'user_id');
    }

    public function role() {
        return $this->hasMany(RoleUser::class, 'user_id');
    }

    public function attendee() {
        return $this->hasMany(EventAttendee::class, 'user_id');
    }

    public function agent(){
        return $this->hasMany(TicketAgentGroups::class, 'agent_id');
    }

    public function group(){
        return $this->hasMany(EmployeeTeam::class, 'user_id');
    }


    public static function allClients()
    {
        return User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', 'client')
            ->get();
    }

    public static function allEmployees($exceptId = NULL)
    {
        $users = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', '<>', 'client');

        if(!is_null($exceptId)){
            $users->where('users.id', '<>', $exceptId);
        }

        $users->groupBy('users.id');
        return $users->get();
    }

    public static function allAdmins($exceptId = NULL)
    {
        $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', 'admin');

        if(!is_null($exceptId)){
            $users->where('users.id', '<>', $exceptId);
        }

        return $users->get();
    }

    public static function teamUsers($teamId)
    {
        $users = User::join('employee_teams', 'employee_teams.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('employee_teams.team_id', $teamId);

        return $users->get();
    }

    public static function userListLatest($userID,$term)
    {

        if($term)
        {
                $termCnd = "and users.name like '%$term%'";
        }
        else {
                $termCnd = '';
        }

        $messageSetting = MessageSetting::first();

        if(auth()->user()->hasRole('admin')){
            if($messageSetting->allow_client_admin == 'no'){
                $termCnd.= "and roles.name != 'client'";
            }
        }
        elseif(auth()->user()->hasRole('employee')){
            if($messageSetting->allow_client_employee == 'no'){
                $termCnd.= "and roles.name != 'client'";
            }
        }
        elseif(auth()->user()->hasRole('client')){
            if($messageSetting->allow_client_admin == 'no'){
                $termCnd.= "and roles.name != 'admin'";
            }
            if($messageSetting->allow_client_employee == 'no'){
                $termCnd.= "and roles.name != 'employee'";
            }
        }

        $query =   DB::select("SELECT * FROM ( SELECT * FROM (
                    SELECT users.id,'0' AS groupId, users.name,  users.image,  users_chat.created_at as last_message, users_chat.message, users_chat.message_seen, users_chat.user_one
                    FROM users
                    INNER JOIN users_chat ON users_chat.from = users.id
                    LEFT JOIN role_user ON role_user.user_id = users.id
                    LEFT JOIN roles ON roles.id = role_user.role_id
                    WHERE users_chat.to = $userID $termCnd
                    UNION
                    SELECT users.id,'0' AS groupId, users.name,users.image, users_chat.created_at  as last_message, users_chat.message, users_chat.message_seen, users_chat.user_one
                    FROM users
                    INNER JOIN users_chat ON users_chat.to = users.id
                    LEFT JOIN role_user ON role_user.user_id = users.id
                    LEFT JOIN roles ON roles.id = role_user.role_id
                    WHERE users_chat.from = $userID  $termCnd
                    ) AS allUsers
                    ORDER BY  last_message DESC
                    ) AS allUsersSorted
                    GROUP BY id
                    ORDER BY  last_message DESC");

        return $query;
    }

    public static function isAdmin($userId){
        $role = Role::where('name', 'admin')->first();
        $user = RoleUser::where('role_id', $role->id)
                ->where('user_id', $userId)
                ->first();

        if(!is_null($user)){
            return true;
        }
        return false;
    }

    public static function isClient($userId){
        $role = Role::where('name', 'client')->first();
        $user = RoleUser::where('role_id', $role->id)
                ->where('user_id', $userId)
                ->first();

        if(!is_null($user)){
            return true;
        }
        return false;
    }

    public static function isEmployee($userId){
        $role = Role::where('name', 'employee')->first();
        $user = RoleUser::where('role_id', $role->id)
                ->where('user_id', $userId)
                ->first();

        if(!is_null($user)){
            return true;
        }
        return false;
    }

}