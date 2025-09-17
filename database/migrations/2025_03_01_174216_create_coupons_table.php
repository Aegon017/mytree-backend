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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();

            // $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            // $table->decimal('discount_value', 10, 2);
            // $table->decimal('minimum_order_value', 10, 2)->default(0);
            // $table->integer('max_uses')->default(1);
            // $table->integer('used_count')->default(0);
            // $table->dateTime('expires_at')->nullable();
            // $table->boolean('status')->default(1);


            $table->enum('type', ['fixed', 'percentage']); // Fixed amount or percentage discount
            $table->double('discount_value');
            $table->integer('usage_limit')->default(1);
            $table->integer('used_count')->default(0);
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
            $table->tinyInteger('status')->default(1); // Active or Inactive
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
