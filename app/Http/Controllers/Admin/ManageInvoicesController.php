<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Invoices\StoreInvoice;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\ModuleSetting;
use App\Notifications\NewInvoice;
use App\Project;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManageInvoicesController extends AdminBaseController
{

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Project';
        $this->pageIcon = 'layers';

        if(!ModuleSetting::checkModule('invoices')){
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->project = Project::findOrFail();
        $this->currencies = Currency::all();
        $this->lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();

        return view('admin.projects.invoices.create', $this->data);
    }

    public function createInvoice(Request $request)
    {
        $this->project = Project::findOrFail($request->id);
        $this->currencies = Currency::all();
        $this->lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();
        return view('admin.projects.invoices.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoice $request)
    {
        $items = $request->input('item_name');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $type = $request->input('type');

        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && $qty < 1) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error('Amount should be a number.');
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error('Item name cannot be blank.');
            }
        }


        $invoice = new Invoice();
        $invoice->project_id = $request->project_id;
        $invoice->invoice_number = $request->invoice_number;
        $invoice->issue_date = Carbon::parse($request->issue_date)->format('Y-m-d');
        $invoice->due_date = Carbon::parse($request->due_date)->format('Y-m-d');
        $invoice->sub_total = $request->sub_total;
        $invoice->total = $request->total;
        $invoice->currency_id = $request->currency_id;
        $invoice->save();

        // Notify client
        $notifyUser = User::withoutGlobalScope('active')->findOrFail($invoice->project->client_id);
        $notifyUser->notify(new NewInvoice($invoice));

        foreach ($items as $key => $item):
            InvoiceItems::create(['invoice_id' => $invoice->id, 'item_name' => $item, 'type' => $type[$key], 'quantity' => $quantity[$key], 'unit_price' => $cost_per_item[$key], 'amount' => $amount[$key]]);
        endforeach;

        $this->logSearchEntry($invoice->id, 'Invoice #'.$invoice->invoice_number, 'admin.all-invoices.show');

        $this->project = Project::findOrFail($request->project_id);
        $view = view('admin.projects.invoices.invoice-ajax', $this->data)->render();
        return Reply::successWithData(__('messages.invoiceCreated'), ['html' => $view]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->project = Project::findOrFail($id);
        return view('admin.projects.invoices.show', $this->data);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        Invoice::destroy($id);
        $this->project = Project::findOrFail($invoice->project_id);
        $view = view('admin.projects.invoices.invoice-ajax', $this->data)->render();
        return Reply::successWithData(__('messages.invoiceDeleted'), ['html' => $view]);
    }

    public function download($id) {
//        header('Content-type: application/pdf');

        $this->invoice = Invoice::findOrFail($id);
        $this->discount = InvoiceItems::where('type', 'discount')
            ->where('invoice_id', $this->invoice->id)
            ->sum('amount');
        $this->taxes = InvoiceItems::where('type', 'tax')
            ->where('invoice_id', $this->invoice->id)
            ->get();

        $this->settings = Setting::findOrFail(1);
        $this->invoiceSetting = InvoiceSetting::first();


        $pdf = app('dompdf.wrapper');
        $pdf->loadView('invoices.'.$this->invoiceSetting->template, $this->data);
        $filename = $this->invoice->invoice_number;
//        return $pdf->stream();
        return $pdf->download($filename . '.pdf');
    }
}
