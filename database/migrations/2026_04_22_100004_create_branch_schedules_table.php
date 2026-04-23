<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu
            $table->unsignedTinyInteger('day_of_week');
            $table->time('open_time')->default('08:00:00');
            $table->time('close_time')->default('21:30:00');
            $table->boolean('is_closed')->default(false); // hari libur
            $table->timestamps();

            $table->unique(['branch_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_schedules');
    }
};
