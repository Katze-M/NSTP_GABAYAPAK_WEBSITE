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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id('Budget_ID');
            $table->text('Resources_Needed')->nullable();
            $table->text('Partner_Agencies')->nullable();
            $table->decimal('Amount', 10, 2)->default(0);
            $table->foreignId('activity_id')->constrained('activities', 'Activity_ID')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};