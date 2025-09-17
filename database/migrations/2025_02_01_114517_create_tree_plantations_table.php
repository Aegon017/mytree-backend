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
        Schema::create('tree_plantations', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('supervisor_id'); // FK to Supervisor
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('tree_id')->constrained('trees')->onDelete('cascade');
            $table->decimal('latitude', 10, 7); // Latitude
            $table->decimal('longitude', 10, 7); // Longitude
            $table->string('geoId'); // Geo ID (or Geographical region ID)
            $table->text('description')->nullable(); // Description of the plantation

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

            // Foreign Key Constraints
            $table->foreign('supervisor_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tree_plantations');
    }
};
