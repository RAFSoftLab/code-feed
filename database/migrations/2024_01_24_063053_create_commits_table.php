<?php

use App\Models\Repository;
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
            $table->string('title');
            $table->string('summary');
            $table->integer('lineCount');
            $table->boolean('hasSecurityIssues');
            $table->boolean('hasBugs');
            $table->string('hash');
            $table->integer('committed_at');
            $table->longText('change')->nullable();
            $table->timestamps();
            $table->foreignIdFor(Repository::class)
                ->constrained()
                ->onDelete('cascade');
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
