<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'empno' => '500500',
                'name' => 'Test User',
                'password' => '1234',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empno' => '200100',
                'name' => 'O. Wickramasinghe',
                'password' => 'hodpass',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empno' => '200200',
                'name' => 'G. Samarasinghe',
                'password' => 'deanpass',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empno' => '100100',
                'name' => 'R. Perera',
                'password' => 'vcpass',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
