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
        // Dean FAS user
        DB::table('personal_details')->insert([
            'empno' => '600600',
            'nic' => '990000000V',
            'name_with_initials' => 'Dr. S. Perera',
            'names_denoted_by_initials' => 'Sunil Perera',
            'department' => "Dean's Office",
            'faculty' => 'Applied Science',
            'designation' => 'Dean FAS',
            'mobile' => '0711234567',
        ]);
    }
}
