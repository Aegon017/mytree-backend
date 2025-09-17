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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who placed the order
            $table->decimal('amount', 10, 2); // Total amount for the order
            $table->string('razorpay_order_id')->unique(); // Razorpay order ID
            $table->string('razorpay_payment_id')->nullable(); // Razorpay payment ID
            $table->string('order_status')->default('pending'); // Order status (pending, paid, shipped, canceled, etc.)
            $table->string('payment_status')->default('pending'); // Payment status (paid, failed, pending)
            $table->text('address')->nullable(); // Shipping address
            $table->timestamp('created_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                ->default(
                    DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                );
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->tinyInteger('trash')->default(0);
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
