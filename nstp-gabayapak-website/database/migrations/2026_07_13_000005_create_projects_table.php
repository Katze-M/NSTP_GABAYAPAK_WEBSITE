<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
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
            $table->string('Project_Section')->nullable();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->json('student_ids')->nullable();
            $table->json('member_roles')->nullable();
            $table->text('Project_Rejection_Reason')->nullable();
            $table->unsignedBigInteger('Project_Rejected_By')->nullable();
            $table->unsignedBigInteger('endorsed_by')->nullable();
            $table->unsignedBigInteger('Project_Approved_By')->nullable();
            $table->unsignedBigInteger('mark_as_completed_by')->nullable();
            $table->boolean('is_resubmission')->default(false);
            $table->text('previous_rejection_reasons')->nullable();
            $table->integer('resubmission_count')->default(0);
            $table->timestamps();

            $table->foreign('Project_Rejected_By')->references('id')->on('users')->onDelete('set null');
            $table->foreign('endorsed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('Project_Approved_By')->references('id')->on('users')->onDelete('set null');
            $table->foreign('mark_as_completed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
