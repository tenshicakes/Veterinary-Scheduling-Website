<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_table', function (Blueprint $table) {
            $table->id('userID');
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->enum('role', ['User', 'Assistant', 'Admin', 'Superadmin'])->default('User');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_table');
    }
};
