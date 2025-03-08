<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class
        ]);

        // DB::table('user_types')->insert([
        //     'user_type' => 'ADMIN',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // DB::table('user_types')->insert([
        //     'user_type' => 'PROFESSOR',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // DB::table('user_types')->insert([
        //     'user_type' => 'STUDENT',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // User::factory()->count(5)->create();
    }
}
