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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('Project_ID');
            $table->string('Project_Name');
            $table->string('Project_Team_Name');
            $table->string('Project_Logo')->nullable();
            $table->string('Project_Component');
            $table->text('Project_Solution');
            $table->text('Project_Goals');
            $table->text('Project_Target_Community');
            $table->text('Project_Expected_Outcomes');
            $table->text('Project_Problems');
            $table->string('Project_Status')->default('draft');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};