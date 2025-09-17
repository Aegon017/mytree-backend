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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name', 100);
            $table->string('botanical_name', 100);
            $table->string('nick_name', 100);
             
            $table->string('slug', 100);
            $table->string('sku', 100);
            $table->double('price');
            $table->double('discount_price');
            $table->string('main_image', 200)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('description', 200)->nullable();
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

            //keys
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
