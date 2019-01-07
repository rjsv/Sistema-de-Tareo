<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;
use Illuminate\Support\Facades\App;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {
        $admin = new Role();
        $admin->name = 'admin';
        $admin->display_name = 'App Administrator'; // optional
        $admin->description = 'Admin is allowed to manage everything of the app.'; // optional
        $admin->save();

        $employee = new Role();
        $employee->name = 'employee';
        $employee->display_name = 'Employee'; // optional
        $employee->description = 'Employee can see tasks and projects assigned to him.'; // optional
        $employee->save();

        $client = new Role();
        $client->name = 'client';
        $client->display_name = 'Client'; // optional
        $client->description = 'Client can see own tasks and projects.'; // optional
        $client->save();


        // Assign admin Role
        $user = User::where('email', '=', 'admin@example.com')->first();
        $user->roles()->attach($admin->id); // id only
        if (App::environment('codecanyon')) {
            $user->roles()->attach($employee->id); // id only
        }

        if (!App::environment('codecanyon')) {
            // Attach employee role to existing users
            $users = User::where('email', '<>', 'client@example.com')->get();

            foreach ($users as $user) {
                $user->roles()->attach($employee->id);
            }

            // Assign client Role
            $user = User::where('email', '=', 'client@example.com')->first();
            $user->roles()->attach($client->id); // id only
        }

    }

}
