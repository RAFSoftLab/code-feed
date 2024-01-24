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
            $table->string('author')->nullable();
            $table->string('message')->nullable();
            $table->string('repository')->nullable();
            $table->string('committer')->nullable();
            $table->string('tree')->nullable();
            $table->integer('created_at')->nullable();
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
