<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Utility;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(NotificationSeeder::class);
        Artisan::call('module:migrate LandingPage');
        Artisan::call('module:seed LandingPage');

        // Check if route is available first
        $routeName = optional(Request::route())->getName();

        if ($routeName !== 'LaravelUpdater::database') {
            $this->call(PlansTableSeeder::class);
            $this->call(UsersTableSeeder::class);
            $this->call(AiTemplateSeeder::class);
        } else {
            Utility::languagecreate();
            User::defaultEmail();
            User::userDefaultData();
        }
    }
}
