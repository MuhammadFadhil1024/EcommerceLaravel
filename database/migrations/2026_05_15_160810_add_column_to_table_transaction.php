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
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('total_price', 'total_payment');
            $table->decimal('courier_cost', 10, 2)->nullable()->after('total_payment');
            $table->string('reference_id', 50)->after('user_id')->unique();
            $table->string('session_id', 50)->after('reference_id')->comment('ID payment method session xendit');
            $table->dateTime('payment_date')->nullable()->after('courier_cost');
            $table->dateTime('expires_at', 3)->nullable()->after('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('total_payment', 'total_price');
            $table->dropColumn('reference_id');
            $table->dropColumn('payment_date');
            $table->dropColumn('expires_at');
        });
    }
};
