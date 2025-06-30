<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formStatuses = [
            [
                'form_stat_id' => 1, 
                'form_status' => 'Draft',
                'description' => 'Form is saved as draft and can be edited by the user'
            ],
            [
                'form_stat_id' => 2, 
                'form_status' => 'Complete',
                'description' => 'Form is submitted and ready for processing by HOD'
            ],
            [
                'form_stat_id' => 3, 
                'form_status' => 'Returned',
                'description' => 'Form is returned to user for corrections with remarks'
            ],
        ];

        foreach ($formStatuses as $formStatus) {
            DB::table('form_statuses')->updateOrInsert(
                ['form_stat_id' => $formStatus['form_stat_id']],
                [
                    'form_status' => $formStatus['form_status'],
                    'description' => $formStatus['description']
                ]
            );
        }
    }
}
