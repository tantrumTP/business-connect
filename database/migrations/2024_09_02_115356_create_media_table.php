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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('type'); // 'photo' or 'video'
            $table->string('caption')->nullable();
            $table->unsignedBigInteger('mediaable_id'); // ID of the associated model
            $table->string('mediaable_type'); // Name of the asociated model
            $table->timestamps();

            $table->index(['mediaable_id', 'mediaable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
