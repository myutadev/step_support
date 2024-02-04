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
        Schema::create('special_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnUpdate()->cascadeOnUpdate();
            $table->foreignId('work_schedule_id')->constrained()->cascadeOnUpdate()->cascadeOnUpdate();;
            $table->foreignId('schedule_type_id')->constrained()->cascadeOnUpdate()->cascadeOnUpdate();;
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_schedules');
    }
};
