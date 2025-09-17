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
        Schema::create('user_tree_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key for users
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Original tree reference
            $table->foreignId('original_tree_id')->constrained('trees')->onDelete('cascade'); // Original tree reference
            $table->foreignId('adopted_tree_id')->constrained('trees')->onDelete('cascade'); // Adopted tree reference
            $table->dateTime('subscription_start'); // Subscription start date
            $table->dateTime('subscription_end'); // Subscription end date
            $table->enum('status', ['active', 'expired'])->default('active'); // Subscription status
            $table->timestamp('created_at')
            ->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updated_at')
            ->default(
                DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tree_relation');
    }
};
