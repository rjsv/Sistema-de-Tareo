<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 23/11/17
 * Time: 6:07 PM
 */

namespace App\Traits;

use App\Currency;
use App\Setting;
use GuzzleHttp\Client;

trait CurrencyExchange{

    public function updateExchangeRates(){
        $currencies = Currency::all();
        $setting = Setting::first();

        foreach($currencies as $currency){

            $currency = Currency::findOrFail($currency->id);

            if($currency->is_cryptocurrency == 'no'){
                // get exchange rate
                $client = new Client();
                $res = $client->request('GET', 'http://free.currencyconverterapi.com/api/v3/convert?q='.$setting->currency->currency_code.'_'.$currency->currency_code);
                $conversionRate = $res->getBody();
                $conversionRate = json_decode($conversionRate, true);

                if(!empty($conversionRate['results'])){
                    $currency->exchange_rate = $conversionRate['results'][strtoupper($setting->currency->currency_code).'_'.$currency->currency_code]['val'];
                }
            }
            else{
                // get exchange rate
                $client = new Client();
                $res = $client->request('GET', 'http://free.currencyconverterapi.com/api/v3/convert?q='.$setting->currency->currency_code.'_USD');
                $conversionRate = $res->getBody();
                $conversionRate = json_decode($conversionRate, true);

                $usdExchangePrice = $conversionRate['results'][strtoupper($setting->currency->currency_code).'_USD']['val'];
                $currency->exchange_rate = $usdExchangePrice;
            }

            $currency->save();
        }


    }

}