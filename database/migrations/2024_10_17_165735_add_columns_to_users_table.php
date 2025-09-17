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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->default(1)->nullable()->comment('1-user,2-company');
            $table->string('mobile_prefix',5)->nullable();
            $table->string('referral_code')->unique()->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();
    
            // Foreign key to reference the user who referred
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           // $table->dropForeign(['referrer_id']);
            // Drop the columns
            $table->dropColumn(['referral_code', 'referrer_id','mobile_prefix','user_type']);
        });
    }
};
