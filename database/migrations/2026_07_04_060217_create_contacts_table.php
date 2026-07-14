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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')
            //       ->constrained()
            //       ->cascadeOnDelete();
            $table->string('phone_num')->unique();
            $table->string('contact_name');
            $table->json('tags')->nullable();
            $table->timestamps();

            // $table->unique(['user_id', 'phone_num']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
