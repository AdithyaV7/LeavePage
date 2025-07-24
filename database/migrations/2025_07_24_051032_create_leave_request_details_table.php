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
        Schema::create('leave_request_details', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no');
            $table->foreign('reference_no')->references('reference_no')->on('leave_details')->onDelete('cascade');
            $table->text('detail')->nullable();
            $table->string('country')->nullable();
            $table->date('travel_from_date')->nullable();
            $table->date('travel_to_date')->nullable();
            $table->json('documents')->nullable(); // Store uploaded document paths as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_request_details');
    }
};
