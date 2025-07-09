<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leave_details', function (Blueprint $table) {
            $table->boolean('vc_recommend_committee')->nullable()->after('dean_reviewed_at');
            $table->boolean('vc_approved_council')->nullable()->after('vc_recommend_committee');
            $table->text('vc_remarks')->nullable()->after('vc_approved_council');
            $table->string('vc_signature')->nullable()->after('vc_remarks');
            $table->string('vc_name')->nullable()->after('vc_signature');
            $table->dateTime('vc_reviewed_at')->nullable()->after('vc_name');
            $table->boolean('vc_checked')->default(false)->after('vc_reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_details', function (Blueprint $table) {
            $table->dropColumn([
                'vc_recommend_committee',
                'vc_approved_council',
                'vc_remarks',
                'vc_signature',
                'vc_name',
                'vc_reviewed_at',
                'vc_checked',
            ]);
        });
    }
};
