<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing leave_details records to populate department_id and faculty_id
        // from the employees table based on matching NIC
        DB::statement("
            UPDATE leave_details 
            SET 
                department_id = (
                    SELECT employees.department_id 
                    FROM employees 
                    WHERE employees.nic = leave_details.nic
                    LIMIT 1
                ),
                faculty_id = (
                    SELECT employees.faculty_id 
                    FROM employees 
                    WHERE employees.nic = leave_details.nic
                    LIMIT 1
                )
            WHERE EXISTS (
                SELECT 1 FROM employees 
                WHERE employees.nic = leave_details.nic
            )
            AND (leave_details.department_id IS NULL OR leave_details.faculty_id IS NULL)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set department_id and faculty_id to NULL for all records
        DB::statement("
            UPDATE leave_details 
            SET department_id = NULL, faculty_id = NULL
        ");
    }
};
