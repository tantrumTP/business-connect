<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(3)
        ->has(
            Business::factory(10)
            ->has(Product::factory(3))
            ->has(Service::factory(2))
            )
        ->create();
    }
}
