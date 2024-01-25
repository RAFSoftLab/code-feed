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
        Schema::create('commits', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->string('author_email');
            $table->string('message');
            $table->string('repository');
            $table->string('organization');
            $table->string('hash');
            $table->integer('created_at');
            $table->integer('committed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commits');
    }
};
