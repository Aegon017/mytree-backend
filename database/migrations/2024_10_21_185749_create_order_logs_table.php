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
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('tree_id')->constrained()->onDelete('cascade'); // Foreign key to products table
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // Store price of the product
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
        Schema::dropIfExists('order_logs');
    }
};
