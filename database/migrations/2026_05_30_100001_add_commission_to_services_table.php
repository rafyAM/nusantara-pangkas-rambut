<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('commission_owner_pct', 5, 2)->default(0)->after('price');
            $table->decimal('commission_kapster_pct', 5, 2)->default(0)->after('commission_owner_pct');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['commission_owner_pct', 'commission_kapster_pct']);
        });
    }
};
