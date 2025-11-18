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
            // Add project_id column
            $table->foreignId('project_id')->nullable()->after('Budget_ID');
            // Add foreign key constraint
            $table->foreign('project_id')->references('Project_ID')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['project_id']);
            // Then drop the column
            $table->dropColumn('project_id');
        });
    }
};