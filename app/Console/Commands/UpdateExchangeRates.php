<?php

namespace App\Console\Commands;

use App\Currency;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-exchange-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the exchange rates for all the currencies in currencies table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currencies = Currency::all();
        $setting = Setting::first();

        foreach($currencies as $currency){

            $currency = Currency::findOrFail($currency->id);

            // get exchange rate
            $client = new Client();
            $res = $client->request('GET', 'http://free.currencyconverterapi.com/api/v3/convert?q='.$setting->currency->currency_code.'_'.$currency->currency_code);
            $conversionRate = $res->getBody();
            $conversionRate = json_decode($conversionRate, true);

            $currency->exchange_rate = $conversionRate['results'][$setting->currency->currency_code.'_'.$currency->currency_code]['val'];
            $currency->save();
        }
    }
}
