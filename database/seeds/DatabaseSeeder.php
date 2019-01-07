<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {
        $this->call(UsersTableSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(OrganisationSettingsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(EmailSettingSeeder::class);
//        $this->call(ModuleSettingsSeeder::class);
        $this->call(SmtpSettingsSeeder::class);
//        $this->call(ProjectAdminRoleSeeder::class);
        $this->call(ThemeSettingsTableSeeder::class);
//        $this->call(LanguageSettingsSeeder::class);
        if (!App::environment('codecanyon')) {
            $this->call(ProjectCategorySeeder::class);
            $this->call(ProjectSeeder::class);
        }
    }

}
