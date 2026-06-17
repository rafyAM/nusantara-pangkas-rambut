<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_day_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('business_date');
            $table->datetime('opened_at');
            $table->datetime('closed_at')->nullable();

            // Ringkasan transaksi
            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('total_gross', 15, 2)->default(0);
            $table->decimal('total_cash', 15, 2)->default(0);
            $table->decimal('total_non_cash', 15, 2)->default(0);

            // Bagi hasil
            $table->decimal('total_komisi_kapster', 15, 2)->default(0);
            $table->decimal('total_fee_kasir', 15, 2)->default(0);
            $table->decimal('total_owner_net', 15, 2)->default(0);

            // Rekonsiliasi kas
            $table->decimal('total_cash_out_operasional', 15, 2)->default(0);
            $table->decimal('modal_awal_hari', 15, 2)->default(0);
            $table->decimal('expected_kas_akhir', 15, 2)->default(0);
            $table->decimal('actual_kas_akhir', 15, 2)->nullable();
            $table->decimal('selisih_kas', 15, 2)->nullable();

            // Status & metadata
            $table->string('status')->default('open'); // open | closed
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('snapshot')->nullable(); // freeze breakdown per shift / per layanan / per kapster
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['branch_id', 'business_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_day_reports');
    }
};
