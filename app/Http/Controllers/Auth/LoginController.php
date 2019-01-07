<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Setting;
use App\Traits\AppBoot;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, AppBoot;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        if(!$this->isLegal()){
            return redirect('verify-purchase');
        }

        $setting = Setting::first();
        return view('auth.login', compact('setting'));
    }

    protected function credentials(\Illuminate\Http\Request $request)
    {
        //return $request->only($this->username(), 'password');
        return ['email' => $request->{$this->username()}, 'password' => $request->password, 'status' => 'active', 'login' => 'enable'];
    }

    protected function redirectTo()
    {
        $user = auth()->user();
        if($user->hasRole('admin')){
            return 'admin/dashboard';
        }
        elseif($user->hasRole('employee')){
            return 'member/dashboard';
        }
        elseif($user->hasRole('client')){
            return 'client/dashboard';
        }

    }
}
