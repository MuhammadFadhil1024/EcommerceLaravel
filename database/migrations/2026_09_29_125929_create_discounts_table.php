<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('code', 50)->nullable()->unique();
            $table->enum('level', ['PRODUCT', 'TRANSACTION']);
            $table->enum('value_type', ['PERCENTAGE', 'FIXED_AMOUNT']);
            $table->decimal('value', 15, 2);
            $table->decimal('max_discount_amount', 15, 2)->nullable();
            $table->decimal('min_purchase_amount', 15, 2)->default(0);
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->unsignedInteger('usage_limit_total')->nullable();
            $table->unsignedInteger('usage_limit_per_user')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_stackable')->default(true);
            $table->integer('priority')->default(0);
            $table->enum('status', ['DRAFT', 'ACTIVE', 'INACTIVE'])->default('DRAFT');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes(); // deleted_at

            // ---- INDEX ----
            // Mempercepat query "cari diskon yang sedang aktif"
            $table->index(['status', 'level', 'start_at', 'end_at'], 'idx_discounts_active');
        });

        // ---- CHECK CONSTRAINT ----
        // Laravel belum punya method bawaan untuk CHECK, jadi pakai raw SQL
        DB::statement('ALTER TABLE discounts ADD CONSTRAINT chk_period CHECK (end_at > start_at)');
        DB::statement('ALTER TABLE discounts ADD CONSTRAINT chk_value CHECK (value > 0)');
        DB::statement("ALTER TABLE discounts ADD CONSTRAINT chk_percent CHECK (value_type <> 'PERCENTAGE' OR value <= 100)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
