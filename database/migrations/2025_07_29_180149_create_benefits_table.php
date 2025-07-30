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
        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->tinyInteger('conditioned')->default(0);
            $table->tinyInteger('each')->default(0);
            $table->tinyInteger('conditioned_efficiency')->default(0);
            $table->tinyInteger('conditioned_seniority')->default(0);
            $table->binary('efficiency_rules')->nullable();
            $table->integer('day_cutoff')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benefits');
    }
};
