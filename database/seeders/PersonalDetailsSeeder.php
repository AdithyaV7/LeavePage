<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalDetailsSeeder extends Seeder
{
    public function run()
    {
        DB::table('personal_details')->insert([
            'empno' => '500500',
            'nic' => '981111111V',
            'name_with_initials' => 'A. Vimukthi',
            'names_denoted_by_initials' => 'Adithya Vimukthi',
            'department' => 'Computer Science',
            'faculty' => 'Applied Science',
            'designation' => 'Instructor',
            'mobile' => '0711000000',
        ]);
    }
}
