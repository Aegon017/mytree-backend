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
        Schema::create('add_ons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->string('name', 60);
            $table->string('image', 200)->nullable();
            $table->string('slug', 60)->unique();
            $table->double('price');
            $table->integer('display_type')
                ->default(1)
                ->nullable()
                ->comment('1-addon,2-gifts');
            $table->integer('cake_type')
                ->default(1)
                ->nullable()
                ->comment('1-egg less,2-egg');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('trash')->default(0);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();

            //keys
            $table->foreign('type_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_ons');
    }
};
