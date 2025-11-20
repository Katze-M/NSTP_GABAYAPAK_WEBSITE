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
            $table->boolean('is_resubmission')->default(false)->after('Project_Rejection_Reason');
            $table->text('previous_rejection_reasons')->nullable()->after('is_resubmission');
            $table->integer('resubmission_count')->default(0)->after('previous_rejection_reasons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['is_resubmission', 'previous_rejection_reasons', 'resubmission_count']);
        });
    }
};