<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Walk-in queue boleh tanpa data customer terdaftar
            $table->foreignId('customer_id')->nullable()->change();

            // Asal reservasi: web (booking online) atau walk_in (dibuat oleh kasir)
            $table->string('source', 20)->default('web')->after('branch_id');
            $table->index('source');

            // Nama tamu untuk walk-in tanpa customer record (opsional)
            $table->string('guest_name')->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn(['source', 'guest_name']);
            // customer_id dikembalikan ke NOT NULL — hanya bisa jika tidak ada data nullable
            $table->foreignId('customer_id')->nullable(false)->change();
        });
    }
};
