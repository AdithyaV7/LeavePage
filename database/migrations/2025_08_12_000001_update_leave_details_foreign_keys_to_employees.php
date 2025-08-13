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
        // Check and drop existing foreign key constraints if they exist
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'leave_details'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE leave_details DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            } catch (Exception $e) {
                // Continue if foreign key doesn't exist
            }
        }
        
        // Change empno column type to match employees.employee_no (unsigned integer)
        Schema::table('leave_details', function (Blueprint $table) {
            $table->unsignedInteger('empno')->change();
        });

        // Update empno values to match employees table
        DB::statement("
            UPDATE leave_details
            SET empno = (
                SELECT employees.employee_no
                FROM employees
                WHERE employees.nic = leave_details.nic
                LIMIT 1
            )
            WHERE EXISTS (
                SELECT 1 FROM employees
                WHERE employees.nic = leave_details.nic
            )
        ");

        // Update department_id and faculty_id from employees table
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
        ");

        Schema::table('leave_details', function (Blueprint $table) {
            // Add new foreign key constraints to employees table
            $table->foreign('empno')->references('employee_no')->on('employees')->onDelete('cascade');
            $table->foreign('nic')->references('nic')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_details', function (Blueprint $table) {
            // Drop foreign key constraints to employees
            $table->dropForeign(['empno']);
            $table->dropForeign(['nic']);
            
            // Add back foreign key constraints to personal_details (if table exists)
            // Note: This assumes personal_details table still exists
            try {
                $table->foreign('empno')->references('empno')->on('personal_details')->onDelete('cascade');
                $table->foreign('nic')->references('nic')->on('personal_details')->onDelete('cascade');
            } catch (Exception $e) {
                // personal_details table might not exist, skip
            }
        });
    }
};
