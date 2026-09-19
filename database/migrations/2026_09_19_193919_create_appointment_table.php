<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_table', function (Blueprint $table) {
            $table->id('appointmentID');
            $table->foreignId('userID')->constrained('users_table', 'userID')->onDelete('cascade');
            $table->foreignId('infoID')->constrained('info_table', 'infoID')->onDelete('cascade');
            $table->foreignId('serviceID')->constrained('service_table', 'serviceID')->onDelete('cascade');
            $table->date('appointmentdate');
            $table->time('appointmenttime');
            $table->enum('status', ['Pending', 'Approved', 'Cancelled', 'Rejected', 'Completed', 'No-Show'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_table');
    }
};