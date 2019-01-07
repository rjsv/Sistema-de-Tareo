<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {
        $user = new User();
        $user->name = 'Admin Name';
        $user->email = 'admin@example.com';
        $user->password = Hash::make('123456');
        $user->save();

        $employee = new \App\EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->job_title = 'Project Manager';
        $employee->address = 'address';
        $employee->hourly_rate = '50';
        $employee->save();

        $search = new \App\UniversalSearch();
        $search->searchable_id = $user->id;
        $search->title = $user->name;
        $search->route_name = 'admin.employees.show';
        $search->save();

        if (!App::environment('codecanyon')) {
            // Employee details
            $user = new User();
            $user->name = 'Employee Name';
            $user->email = 'employee@example.com';
            $user->password = Hash::make('123456');
            $user->save();

            $search = new \App\UniversalSearch();
            $search->searchable_id = $user->id;
            $search->title = $user->name;
            $search->route_name = 'admin.employees.show';
            $search->save();

            $employee = new \App\EmployeeDetails();
            $employee->user_id = $user->id;
            $employee->job_title = 'Developer';
            $employee->address = 'address';
            $employee->hourly_rate = '25';
            $employee->save();

            // Client details
            $user = new User();
            $user->name = 'Client Name';
            $user->email = 'client@example.com';
            $user->password = Hash::make('123456');
            $user->save();

            $search = new \App\UniversalSearch();
            $search->searchable_id = $user->id;
            $search->title = $user->name;
            $search->route_name = 'admin.clients.projects';
            $search->save();

            $client = new \App\ClientDetails();
            $client->user_id = $user->id;
            $client->company_name = 'Company Name';
            $client->address = 'Company address';
            $client->website = 'www.domain-name.com';
            $client->save();


            factory(User::class, 50)->create();
        }
    }

}
