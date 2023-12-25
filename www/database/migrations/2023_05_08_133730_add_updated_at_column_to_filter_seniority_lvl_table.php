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
        Schema::table('filter_seniority_lvl', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });
        Schema::table('filter_seniority_lvl', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filter_seniority_lvl', function (Blueprint $table) {
            //
        });
    }
};
