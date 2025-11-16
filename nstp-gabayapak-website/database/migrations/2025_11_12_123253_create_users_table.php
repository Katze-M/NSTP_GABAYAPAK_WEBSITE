<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_Name')->nullable();
            $table->string('user_Email')->unique();
            $table->string('user_Password');
            $table->string('user_Type')->default('student'); // 'student' or 'staff'
            $table->string('user_role'); // Student, NSTP Formator, NSTP Program Officer, SACSI Director, and SACSI Admin Staff
            $table->rememberToken();
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};