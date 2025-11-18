<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add foreign key constraint linking Project_Rejected_By -> users.user_id
        Schema::table('projects', function (Blueprint $table) {
            // Ensure column exists before attempting to add fk
            if (Schema::hasColumn('projects', 'Project_Rejected_By')) {
                $table->foreign('Project_Rejected_By', 'fk_projects_rejected_by_users')
                    ->references('user_id')->on('users')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'Project_Rejected_By')) {
                $table->dropForeign('fk_projects_rejected_by_users');
            }
        });
    }
};
