<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultPlans = [
            [
                'name' => 'Free Plan',
                'price' => 0,
                'duration' => 'Lifetime',
                'max_users' => 1,
                'max_employees' => 5,
                'storage_limit' => 1024,
                'enable_chatgpt' => true,
                'image' => 'free_plan.png',
            ],
        ];

        $existingPlanNames = Plan::pluck('name')->toArray();

        $plansToInsert = collect($defaultPlans)
            ->reject(fn($plan) => in_array($plan['name'], $existingPlanNames))
            ->map(function ($plan) {
                return array_merge($plan, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })
            ->values()
            ->all();

        if (!empty($plansToInsert)) {
            Plan::insert($plansToInsert);
        }
    }
}
