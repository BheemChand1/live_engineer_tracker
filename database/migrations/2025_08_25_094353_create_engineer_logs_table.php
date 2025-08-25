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
        Schema::create('engineer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engineer_id')->constrained()->onDelete('cascade');
            $table->datetime('login_at');
            $table->datetime('logout_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineer_logs');
    }
};
