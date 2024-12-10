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
        Schema::create('info_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('requestable_id')->nullable();
            $table->string('requestable_type')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('message');
            $table->boolean('attended')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index(['requestable_id', 'requestable_type']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_requests');
    }
};
