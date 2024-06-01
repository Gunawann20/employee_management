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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false)->unique();
            $table->string('name')->nullable(false);
            $table->string('image');
            $table->enum('gender', ['male', 'female'])->nullable(false);
            $table->date('birthdate')->nullable(false);
            $table->integer('salary')->nullable(false);
            $table->string('work_place')->nullable(false);
            $table->string('position')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
