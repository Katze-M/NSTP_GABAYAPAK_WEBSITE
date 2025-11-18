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
        Schema::table('budgets', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['activity_id']);
            // Then drop the column
            $table->dropColumn('activity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Add the column back
            $table->foreignId('activity_id')->nullable()->after('Amount');
            // Recreate the foreign key constraint
            $table->foreign('activity_id')->references('Activity_ID')->on('activities')->onDelete('cascade');
        });
    }
};