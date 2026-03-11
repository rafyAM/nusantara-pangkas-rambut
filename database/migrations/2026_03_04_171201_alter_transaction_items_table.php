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
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->enum('item_type', ['service', 'product'])->default('service')->after('transaction_id');
            $table->foreignId('product_id')->nullable()->after('service_id')->constrained('products')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->after('product_id')->constrained('employees')->nullOnDelete();

            $table->unsignedBigInteger('service_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable(false)->change();

            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->dropColumn('item_type');
        });
    }
};
