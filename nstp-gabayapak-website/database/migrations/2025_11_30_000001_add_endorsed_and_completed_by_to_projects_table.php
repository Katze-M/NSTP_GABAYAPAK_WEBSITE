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
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('endorsed_by')->nullable()->after('Project_Rejected_By');
            $table->unsignedBigInteger('mark_as_completed_by')->nullable()->after('Project_Approved_By');
            $table->foreign('endorsed_by')->references('user_id')->on('users')->nullOnDelete();
            $table->foreign('mark_as_completed_by')->references('user_id')->on('users')->nullOnDelete();
            $table->string('Project_Status')->default('draft')->change(); // For new status values
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['endorsed_by']);
            $table->dropColumn('endorsed_by');
            $table->dropForeign(['mark_as_completed_by']);
            $table->dropColumn('mark_as_completed_by');
        });
    }
};
