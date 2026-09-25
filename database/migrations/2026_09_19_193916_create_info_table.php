<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_table', function (Blueprint $table) {
            $table->id('infoID');
            $table->foreignId('userID')->constrained('users_table', 'userID')->onDelete('cascade');
            $table->string('petname');
            $table->string('petspecies'); // Cat or Dog
            $table->string('petbreed')->nullable();
            $table->string('userimage')->nullable();
            $table->string('petimage')->nullable();
            $table->boolean('is_archived')->default(false)->after('petimage');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_table');
    }
};
