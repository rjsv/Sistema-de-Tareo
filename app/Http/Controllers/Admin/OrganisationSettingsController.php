<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\EmployeeDetails;
use App\Helper\Reply;
use App\Http\Requests\Settings\UpdateOrganisationSettings;
use App\Setting;
use App\Traits\CurrencyExchange;
use Hamcrest\Core\Set;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class OrganisationSettingsController extends AdminBaseController
{

    use CurrencyExchange;

    public function __construct() {
        parent:: __construct();
        $this->pageTitle = __('app.menu.accountSettings');
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $setting = Setting::first();
        $this->currencies = Currency::all();

        if(!$setting){
            abort(404);
        }

        return view('admin.settings.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrganisationSettings $request, $id)
    {
        $setting = Setting::findOrFail($id);
        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        $setting->currency_id = $request->input('currency_id');
        $setting->timezone = $request->input('timezone');
        $setting->locale = $request->input('locale');
        $setting->latitude = $request->input('latitude');
        $setting->longitude = $request->input('longitude');

        if ($request->hasFile('logo')) {
            $setting->logo = $request->logo->hashName();
            $request->logo->store('user-uploads/app-logo');
        }
        $setting->last_updated_by = $this->user->id;

        if ($request->hasFile('login_background')) {
            $request->login_background->storeAs('user-uploads', 'login-background.jpg');
            $setting->login_background = 'login-background.jpg';
        }
        $setting->save();

        $this->updateExchangeRates();

        return Reply::redirect(route('admin.settings.index'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function changeLanguage(Request $request) {
        $setting = Setting::first();
        $setting->locale = $request->input('lang');

        $setting->last_updated_by = $this->user->id;
        $setting->save();

        return Reply::success('Language changed successfully.');
    }
}
