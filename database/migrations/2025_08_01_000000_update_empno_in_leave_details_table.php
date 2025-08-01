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
        // First, check if empno column exists and is integer type
        $columns = Schema::getColumnListing('leave_details');
        $empnoExists = in_array('empno', $columns);
        
        if ($empnoExists) {
            // Get column type
            $empnoColumn = DB::select("SHOW COLUMNS FROM leave_details WHERE Field = 'empno'")[0];
            $isInteger = strpos(strtolower($empnoColumn->Type), 'int') !== false;
            
            if ($isInteger) {
                // Drop the existing foreign key constraint if it exists
                try {
                    Schema::table('leave_details', function (Blueprint $table) {
                        $table->dropForeign(['empno']);
                    });
                } catch (Exception $e) {
                    // Foreign key might not exist, continue
                }
                
                // Change empno from integer to string
                Schema::table('leave_details', function (Blueprint $table) {
                    $table->string('empno')->change();
                });
                
                // Update existing records to populate empno from personal_details
                DB::statement("
                    UPDATE leave_details 
                    SET empno = (
                        SELECT personal_details.empno 
                        FROM personal_details 
                        WHERE personal_details.nic = leave_details.nic
                    )
                    WHERE empno IS NULL OR empno = 0
                ");
                
                // Add foreign key constraint
                Schema::table('leave_details', function (Blueprint $table) {
                    $table->foreign('empno')->references('empno')->on('personal_details')->onDelete('cascade');
                });
            }
        } else {
            // Add empno column if it doesn't exist
            Schema::table('leave_details', function (Blueprint $table) {
                $table->string('empno')->after('id');
                $table->foreign('empno')->references('empno')->on('personal_details')->onDelete('cascade');
            });
            
            // Populate empno from personal_details
            DB::statement("
                UPDATE leave_details 
                SET empno = (
                    SELECT personal_details.empno 
                    FROM personal_details 
                    WHERE personal_details.nic = leave_details.nic
                )
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_details', function (Blueprint $table) {
            $table->dropForeign(['empno']);
            $table->integer('empno')->change();
        });
    }
};
