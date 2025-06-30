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
    Schema::create('personal_details', function (Blueprint $table) {
        $table->id();
        $table->string('empno')->unique();
        $table->string('nic')->unique();
        $table->string('name_with_initials');
        $table->string('names_denoted_by_initials')->nullable();
        $table->string('department')->nullable();
        $table->string('faculty')->nullable();
        $table->string('designation')->nullable();
        $table->string('mobile')->nullable();
        $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_details');
    }
};
