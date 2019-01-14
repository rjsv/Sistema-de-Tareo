<?php

use Illuminate\Database\Seeder;
use App\Setting;
use App\Currency;


class OrganisationSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $currency = Currency::where('currency_code', 'USD')->first();

        $setting = new Setting();
        $setting->company_name = 'Backus';
        $setting->company_email = 'informes@backus.com.pe';
        $setting->company_phone = '1234567891';
        $setting->address = 'La variante';
        $setting->website = 'www.domain.com';
        $setting->currency_id = $currency->id;
        $setting->timezone = 'Asia/Kolkata';
        $setting->save();
    }

}
