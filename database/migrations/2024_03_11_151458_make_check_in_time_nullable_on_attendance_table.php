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
        Schema::table('attendances', function (Blueprint $table) {
            // check_in_timeカラムをNULL許容に変更
            $table->time('check_in_time')->nullable()->change();
            $table->decimal('body_temp', 5, 2)->nullable()->change();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
            // check_in_timeカラムをNULL不許容に戻す
            $table->time('check_in_time')->nullable(false)->change();
            $table->decimal('body_temp', 5, 2)->nullable(false)->change();;
        });
    }
};
