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
        Schema::table('tasks', function (Blueprint $table) {
            // Rename deadline to due_date and make it nullable
            $table->renameColumn('deadline', 'due_date');
            
            // Drop location column and add customer fields
            $table->dropColumn('location');
            $table->string('customer_name')->nullable()->after('description');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->text('customer_address')->nullable()->after('customer_phone');
            
            // Add device information
            $table->string('device_type')->nullable()->after('customer_address');
            $table->decimal('estimated_hours', 5, 2)->nullable()->after('device_type');
            
            // Update status enum to include 'cancelled'
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending')->change();
            
            // Make description nullable
            $table->text('description')->nullable()->change();
        });
        
        // Make due_date nullable in a separate statement
        Schema::table('tasks', function (Blueprint $table) {
            $table->datetime('due_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('due_date', 'deadline');
            $table->datetime('deadline')->nullable(false)->change();
            
            // Remove new columns
            $table->dropColumn(['customer_name', 'customer_phone', 'customer_address', 'device_type', 'estimated_hours']);
            
            // Add back location column
            $table->string('location')->after('description');
            
            // Revert status enum
            $table->enum('status', ['pending', 'in-progress', 'completed'])->default('pending')->change();
            
            // Make description required
            $table->text('description')->nullable(false)->change();
        });
    }
};
