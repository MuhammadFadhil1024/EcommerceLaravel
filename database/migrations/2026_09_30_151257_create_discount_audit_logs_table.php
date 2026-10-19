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
        Schema::create('discount_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')
                  ->constrained('discounts')
                  ->cascadeOnDelete(); // ON DELETE CASCADE
            $table->foreignId('admin_id')
                  ->constrained('users')
                  ->cascadeOnDelete(); // ON DELETE CASCADE
            $table->enum('action', ['CREATE', 'UPDATE', 'DELETE']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_audit_logs');
    }
};
