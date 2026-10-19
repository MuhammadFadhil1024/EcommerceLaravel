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
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->foreignId('discount_id')->after('user_id')->nullable()->constrained('discounts')->onDelete('set null');
            $table->decimal('price', 15, 2)->after('quantity')->default(0);
            $table->decimal('discount_amount', 15, 2)->after('price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropColumn('discount_id');
            $table->dropColumn('price');
            $table->dropColumn('discount_amount');
        });
    }
};
