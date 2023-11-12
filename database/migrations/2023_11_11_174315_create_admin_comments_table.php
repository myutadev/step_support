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
        Schema::create('admin_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('admin_description')->nullable();
            $table->text('admin_comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('admin_comments');
        Schema::enableForeignKeyConstraints();
    }
};
