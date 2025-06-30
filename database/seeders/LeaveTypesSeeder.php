<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypesSeeder extends Seeder
{
    public function run()
    {
        DB::table('leave_types')->insert([
            ['name' => 'Conference'],
            ['name' => 'Seminar'],
            ['name' => 'Training'],
            ['name' => 'Workshop'],
            ['name' => 'Study Leave'],
        ]);
    }
}
