<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Currency\StoreCurrency;
use App\Traits\CurrencyExchange;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencySettingController extends AdminBaseController
{
    use CurrencyExchange;

    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-settings';
        $this->pageTitle = __('app.menu.currencySettings');
    }

    public function index() {
        $this->currencies = Currency::all();
        return view('admin.currencies.index', $this->data);
    }

    public function create() {
        return view('admin.currencies.create', $this->data);
    }

    public function edit($id) {
        $this->currency = Currency::findOrFail($id);
        return view('admin.currencies.edit', $this->data);
    }

    public function store(StoreCurrency $request) {

        $currency = new Currency();
        $currency->currency_name = $request->currency_name;
        $currency->currency_symbol = $request->currency_symbol;
        $currency->currency_code = $request->currency_code;
        $currency->usd_price = $request->usd_price;
        $currency->is_cryptocurrency = $request->is_cryptocurrency;

        if($request->is_cryptocurrency == 'no'){
            // get exchange rate
            $client = new Client();
            $res = $client->request('GET', 'http://free.currencyconverterapi.com/api/v3/convert?q='.$this->global->currency->currency_code.'_'.$currency->currency_code, ['verify' => false]);
            $conversionRate = $res->getBody();
            $conversionRate = json_decode($conversionRate, true);

            if(!empty($conversionRate['results'])){
                $currency->exchange_rate = $conversionRate['results'][strtoupper($this->global->currency->currency_code.'_'.$currency->currency_code)]['val'];
            }
        }
        else{

            if($this->global->currency->currency_code != 'USD'){
                // get exchange rate
                $client = new Client();
                $res = $client->request('GET', 'http://free.currencyconverterapi.com/api/v3/convert?q='.$this->global->currency->currency_code.'_USD', ['verify' => false]);
                $conversionRate = $res->getBody();
                $conversionRate = json_decode($conversionRate, true);

                $usdExchangePrice = $conversionRate['results'][strtoupper($this->global->currency->currency_code).'_USD']['val'];
                $currency->exchange_rate = ceil(($currency->usd_price/$usdExchangePrice));
            }
        }

        $currency->save();

        $this->updateExchangeRates();

        return Reply::redirect(route('admin.currency.edit', $currency->id), __('messages.currencyAdded'));
    }

    public function update(StoreCurrency $request, $id) {
        $currency = Currency::findOrFail($id);
        $currency->currency_name = $request->currency_name;
        $currency->currency_symbol = $request->currency_symbol;
        $currency->currency_code = $request->currency_code;
        $currency->exchange_rate = $request->exchange_rate;

        $currency->usd_price = $request->usd_price;
        $currency->is_cryptocurrency = $request->is_cryptocurrency;

        if($request->is_cryptocurrency == 'no'){
            // get exchange rate
            $client = new Client();
            $res = $client->request('GET', 'http://free.currencyconverterapi.com/api/v3/convert?q='.$this->global->currency->currency_code.'_'.$currency->currency_code, ['verify' => false]);
            $conversionRate = $res->getBody();
            $conversionRate = json_decode($conversionRate, true);

            if(!empty($conversionRate['results'])){
                $currency->exchange_rate = $conversionRate['results'][strtoupper($this->global->currency->currency_code).'_'.$currency->currency_code]['val'];
            }
        }
        else{

            if($this->global->currency->currency_code != 'USD'){
                // get exchange rate
                $client = new Client();
                $res = $client->request('GET', 'http://free.currencyconverterapi.com/api/v3/convert?q='.$this->global->currency->currency_code.'_USD', ['verify' => false]);
                $conversionRate = $res->getBody();
                $conversionRate = json_decode($conversionRate, true);

                $usdExchangePrice = $conversionRate['results'][strtoupper($this->global->currency->currency_code).'_USD']['val'];
                $currency->exchange_rate = $usdExchangePrice;
            }
        }

        $currency->save();


        $this->updateExchangeRates();


        return Reply::success(__('messages.currencyUpdated'));
    }

    public function destroy($id) {
        if($this->global->currency_id == $id){
           return Reply::error(__('modules.currencySettings.cantDeleteDefault'));
        }
        Currency::destroy($id);
        return Reply::success(__('messages.currencyDeleted'));
    }

    public function exchangeRate($currency){
        // get exchange rate
        $client = new Client();
        $res = $client->request('GET', 'http://free.currencyconverterapi.com/api/v3/convert?q='.$this->global->currency->currency_code.'_'.$currency, ['verify' => false]);
        $conversionRate = $res->getBody();
        $conversionRate = json_decode($conversionRate, true);

        return $conversionRate['results'][strtoupper($this->global->currency->currency_code).'_'.$currency]['val'];
    }

    public function updateExchangeRate(){
        $this->updateExchangeRates();
        return Reply::success(__('messages.exchangeRateUpdateSuccess'));
    }
}
