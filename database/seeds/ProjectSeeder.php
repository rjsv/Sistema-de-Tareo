<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Payment;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $project = new \App\Project();
        $project->project_name = 'Test project';
        $project->project_summary = 'Obcaecati doloremque consectetur, dolore aute non dolor odio ut aut sed et illo laudantium, aliqua. Minus esse excepturi esse aliquip excepteur labore elit, qui omnis voluptas aut dolorum magnam doloremque irure ut veritatis exercitationem aut occaecat qui praesentium quas sed cum elit, ratione exercitation placeat, pariatur? Quas consectetur, tempor incidunt, aliquid voluptatem, velit mollit et illum, adipisicing ea officia aliquam placeat, laborum. In libero natus velit non est aut libero quo ducimus, voluptate officiis est, ut rem aut quam optio, deleniti.';
        $project->start_date = \Carbon\Carbon::parse('03/25/2017')->format('Y-m-d');
        $project->deadline = \Carbon\Carbon::parse('08/25/2017')->format('Y-m-d');
        $project->notes = 'Quas consectetur, tempor incidunt, aliquid voluptatem, velit mollit et illum, adipisicing ea officia aliquam placeat';
        $project->category_id = '1';
        $project->client_id = '3';
        $project->completion_percent = '60';
        $project->save();

        $activity = new \App\ProjectActivity();
        $activity->project_id = $project->id;
        $activity->activity = ucwords($project->project_name) . ' added as new project.';
        $activity->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $project->id;
        $search->title = $project->project_name;
        $search->route_name = 'admin.projects.show';
        $search->save();

        // Assign member
        $member = new \App\ProjectMember();
        $member->user_id = '1';
        $member->project_id = $project->id;
        $member->save();

        $activity = new \App\ProjectActivity();
        $activity->project_id = $project->id;
        $activity->activity = 'New member added to the project.';
        $activity->save();


        $member = new \App\ProjectMember();
        $member->user_id = '2';
        $member->project_id = $project->id;
        $member->save();

        $activity = new \App\ProjectActivity();
        $activity->project_id = $project->id;
        $activity->activity = 'New member added to the project.';
        $activity->save();

        //create task
        $task = new \App\Task();
        $task->heading = 'Task 1';
        $task->description = 'olore aute non dolor odio ut aut sed et illo laudantium, aliqua.';
        $task->due_date = \Carbon\Carbon::parse('05/22/2017')->format('Y-m-d');
        $task->user_id = '2';
        $task->project_id = $project->id;
        $task->priority = 'high';
        $task->status = 'completed';
        $task->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $task->id;
        $search->title = $task->heading;
        $search->route_name = 'admin.all-tasks.edit';
        $search->save();

        $activity = new \App\ProjectActivity();
        $activity->project_id = $project->id;
        $activity->activity = 'New task added to the project.';
        $activity->save();

        $task = new \App\Task();
        $task->heading = 'Task 2';
        $task->description = 'olore aute non dolor odio ut aut sed et illo laudantium, aliqua.';
        $task->due_date = \Carbon\Carbon::parse('05/25/2017')->format('Y-m-d');
        $task->user_id = '2';
        $task->project_id = $project->id;
        $task->priority = 'high';
        $task->status = 'incomplete';
        $task->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $task->id;
        $search->title = $task->heading;
        $search->route_name = 'admin.all-tasks.edit';
        $search->save();

        $activity = new \App\ProjectActivity();
        $activity->project_id = $project->id;
        $activity->activity = 'New task added to the project.';
        $activity->save();

        $task = new \App\Task();
        $task->heading = 'Task 3';
        $task->description = 'aliquam placeat, laborum. In libero natus velit non est aut libero quo ducimus,';
        $task->due_date = \Carbon\Carbon::parse('06/03/2017')->format('Y-m-d');
        $task->user_id = '2';
        $task->project_id = $project->id;
        $task->priority = 'high';
        $task->status = 'incomplete';
        $task->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $task->id;
        $search->title = $task->heading;
        $search->route_name = 'admin.all-tasks.show';
        $search->save();

        $activity = new \App\ProjectActivity();
        $activity->project_id = $project->id;
        $activity->activity = 'New task added to the project.';
        $activity->save();

        //create invoice
        $items = ['item 1', 'item 2'];
        $cost_per_item = ['1000', '1500'];
        $quantity = ['1', '1'];
        $amount = ['1000', '1500'];
        $type = ['item', 'item'];

        $invoice = new \App\Invoice();
        $invoice->project_id = $project->id;
        $invoice->invoice_number = 'INV#01';
        $invoice->issue_date = \Carbon\Carbon::parse('04/21/2017')->format('Y-m-d');
        $invoice->due_date = \Carbon\Carbon::parse('05/01/2017')->format('Y-m-d');
        $invoice->sub_total = '2500';
        $invoice->total = '2500';
        $invoice->currency_id = '1';
        $invoice->status = 'paid';
        $invoice->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $invoice->id;
        $search->title = 'Invoice '.$invoice->invoice_number;
        $search->route_name = 'admin.all-invoices.show';
        $search->save();

        foreach ($items as $key => $item):
            \App\InvoiceItems::create(['invoice_id' => $invoice->id, 'item_name' => $item, 'type' => $type[$key], 'quantity' => $quantity[$key], 'unit_price' => $cost_per_item[$key], 'amount' => $amount[$key]]);
        endforeach;


        $payment = new Payment();
        $payment->invoice_id = $invoice->id;
        $payment->amount = $invoice->total;
        $payment->gateway = 'cash';
        $payment->transaction_id = '123';
        $payment->paid_on = Carbon::parse($invoice->due_date)->format('Y-m-d');
        $payment->status = 'complete';
        $payment->save();

        $items = ['item 3', 'item 4'];
        $cost_per_item = ['1200', '1700'];
        $quantity = ['2', '1'];
        $amount = ['2400', '1700'];
        $type = ['item', 'item'];

        $invoice = new \App\Invoice();
        $invoice->project_id = $project->id;
        $invoice->invoice_number = 'INV#02';
        $invoice->issue_date = \Carbon\Carbon::parse('05/24/2017')->format('Y-m-d');
        $invoice->due_date = \Carbon\Carbon::parse('06/03/2017')->format('Y-m-d');
        $invoice->sub_total = '4100';
        $invoice->total = '4100';
        $invoice->currency_id = '1';
        $invoice->status = 'paid';
        $invoice->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $invoice->id;
        $search->title = 'Invoice '.$invoice->invoice_number;
        $search->route_name = 'admin.all-invoices.show';
        $search->save();

        foreach ($items as $key => $item):
            \App\InvoiceItems::create(['invoice_id' => $invoice->id, 'item_name' => $item, 'type' => $type[$key], 'quantity' => $quantity[$key], 'unit_price' => $cost_per_item[$key], 'amount' => $amount[$key]]);
        endforeach;

        $payment = new Payment();
        $payment->invoice_id = $invoice->id;
        $payment->amount = $invoice->total;
        $payment->gateway = 'cash';
        $payment->transaction_id = '1234';
        $payment->paid_on = Carbon::parse($invoice->due_date)->format('Y-m-d');
        $payment->status = 'complete';
        $payment->save();

        $items = ['item 5'];
        $cost_per_item = ['1700'];
        $quantity = ['1'];
        $amount = ['1700'];
        $type = ['item'];

        $invoice = new \App\Invoice();
        $invoice->project_id = $project->id;
        $invoice->invoice_number = 'INV#03';
        $invoice->issue_date = \Carbon\Carbon::parse('05/25/2017')->format('Y-m-d');
        $invoice->due_date = \Carbon\Carbon::parse('07/03/2017')->format('Y-m-d');
        $invoice->sub_total = '1700';
        $invoice->total = '1700';
        $invoice->currency_id = '1';
        $invoice->status = 'paid';
        $invoice->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $invoice->id;
        $search->title = 'Invoice '.$invoice->invoice_number;
        $search->route_name = 'admin.all-invoices.show';
        $search->save();

        foreach ($items as $key => $item):
            \App\InvoiceItems::create(['invoice_id' => $invoice->id, 'item_name' => $item, 'type' => $type[$key], 'quantity' => $quantity[$key], 'unit_price' => $cost_per_item[$key], 'amount' => $amount[$key]]);
        endforeach;

        $payment = new Payment();
        $payment->invoice_id = $invoice->id;
        $payment->amount = $invoice->total;
        $payment->gateway = 'cash';
        $payment->transaction_id = '12345';
        $payment->paid_on = Carbon::parse($invoice->due_date)->format('Y-m-d');
        $payment->status = 'complete';
        $payment->save();

        //Create time logs
        $timeLog = new \App\ProjectTimeLog();
        $timeLog->project_id = $project->id;
        $timeLog->user_id = '2';
        $timeLog->start_time = \Carbon\Carbon::parse('05/22/2017')->format('Y-m-d').' '.\Carbon\Carbon::parse('07:15 AM')->format('H:i:s');
        $timeLog->start_time = Carbon::createFromFormat('Y-m-d H:i:s', $timeLog->start_time, 'Asia/Kolkata')->setTimezone('UTC');
        $timeLog->end_time = Carbon::parse('05/22/2017')->format('Y-m-d').' '.Carbon::parse('04:15 PM')->format('H:i:s');
        $timeLog->end_time = Carbon::createFromFormat('Y-m-d H:i:s', $timeLog->end_time, 'Asia/Kolkata')->setTimezone('UTC');
        $timeLog->total_hours = $timeLog->end_time->diff($timeLog->start_time)->format('%d')*24+$timeLog->end_time->diff($timeLog->start_time)->format('%H');

        if($timeLog->total_hours == 0){
            $timeLog->total_hours = round(($timeLog->end_time->diff($timeLog->start_time)->format('%i')/60),2);
        }

        $timeLog->memo = 'working on database';
        $timeLog->save();

        $timeLog = new \App\ProjectTimeLog();
        $timeLog->project_id = $project->id;
        $timeLog->user_id = '2';
        $timeLog->start_time = \Carbon\Carbon::parse('05/23/2017')->format('Y-m-d').' '.\Carbon\Carbon::parse('08:15 AM')->format('H:i:s');
        $timeLog->start_time = Carbon::createFromFormat('Y-m-d H:i:s', $timeLog->start_time, 'Asia/Kolkata')->setTimezone('UTC');
        $timeLog->end_time = Carbon::parse('05/23/2017')->format('Y-m-d').' '.Carbon::parse('04:15 PM')->format('H:i:s');
        $timeLog->end_time = Carbon::createFromFormat('Y-m-d H:i:s', $timeLog->end_time, 'Asia/Kolkata')->setTimezone('UTC');
        $timeLog->total_hours = $timeLog->end_time->diff($timeLog->start_time)->format('%d')*24+$timeLog->end_time->diff($timeLog->start_time)->format('%H');

        if($timeLog->total_hours == 0){
            $timeLog->total_hours = round(($timeLog->end_time->diff($timeLog->start_time)->format('%i')/60),2);
        }

        $timeLog->memo = 'working on database';
        $timeLog->save();

        //create issues
        $issue = new \App\Issue();
        $issue->description = 'In libero natus velit non est aut libero quo ducimus';
        $issue->user_id = '3';
        $issue->project_id = $project->id;
        $issue->status = 'resolved';
        $issue->save();

        $issue = new \App\Issue();
        $issue->description = 'velit non est aut libero quo ducimus In libero natus ';
        $issue->user_id = '3';
        $issue->project_id = $project->id;
        $issue->save();

        $issue = new \App\Issue();
        $issue->description = 'In libero natus In libero natus In libero natus ';
        $issue->user_id = '3';
        $issue->project_id = $project->id;
        $issue->save();
    }
}
