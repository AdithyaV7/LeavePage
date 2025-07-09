<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['stat_id' => 1, 'status' => 'Approved'],
            ['stat_id' => 2, 'status' => 'Rejected'],
            ['stat_id' => 3, 'status' => 'Editing'],
            ['stat_id' => 4, 'status' => 'Processing MA'],
            ['stat_id' => 5, 'status' => 'Processing HOD'],
            ['stat_id' => 6, 'status' => 'Processing Dean'],
            ['stat_id' => 7, 'status' => 'Processing VC'],
            ['stat_id' => 8, 'status' => 'VC Checked'],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['stat_id' => $status['stat_id']],
                ['status' => $status['status']]
            );
        }
    }
}

