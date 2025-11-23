<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert any Project_Status = 'current' to 'approved'.
     */
    public function up(): void
    {
        // Convert any legacy 'current' statuses back to 'approved' to standardize on 'approved'
        DB::table('projects')
            ->where('Project_Status', 'current')
            ->update(['Project_Status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     * Convert any Project_Status = 'approved' back to 'current'.
     * Use with caution: this will revert the up() migration.
     */
    public function down(): void
    {
        DB::table('projects')
            ->where('Project_Status', 'approved')
            ->update(['Project_Status' => 'current']);
    }
};
