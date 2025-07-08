<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_details', function (Blueprint $table) {
            $table->id();
            $table->string('nic');
            $table->foreign('nic')->references('nic')->on('personal_details')->onDelete('cascade');

            $table->string('reference_no')->unique();
            $table->unsignedBigInteger('leave_type_id')->nullable();
            $table->foreign('leave_type_id')->references('id')->on('leave_types');

            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('duration')->nullable();
            $table->string('leave_document')->nullable();
            $table->string('consent_letter')->nullable();

            $table->tinyInteger('form_status')->default(4);
            $table->integer('status_id');
            $table->foreign('status_id')->references('stat_id')->on('statuses');

            $table->date('applied_date');
            $table->text('remark')->nullable();
            // HOD review fields
            $table->boolean('hod_adequate_staff')->nullable();
            $table->boolean('hod_teaching_covered')->nullable();
            $table->boolean('hod_exam_work_completed')->nullable();
            $table->boolean('hod_recommend')->nullable();
            $table->text('hod_not_recommend_reason')->nullable();
            $table->text('hod_other_remarks')->nullable();
            $table->string('hod_reviewed_by')->nullable();
            $table->dateTime('hod_reviewed_at')->nullable();
            $table->string('hod_signature')->nullable();
            // Dean review fields (for future use)
            $table->boolean('dean_recommend')->nullable();
            $table->text('dean_remarks')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_details');
    }
};
