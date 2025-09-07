<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['Not Started', 'In Progress', 'On Hold', 'Completed'])->default('Not Started');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // admin who created it
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // assigned user (optional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
