<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('admins')->insert([
            'user_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('admins')->insert([
            'user_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('admins')->insert([
            'user_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('admins')->insert([
            'user_id' => 4,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('admins')->insert([
            'user_id' => 5,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
