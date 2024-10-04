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
        Schema::create('stock_requisitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->string('number_format', 50)->nullable();
            $table->integer('number')->nullable();
            $table->string('code_number', 20)->nullable();
            $table->foreignId('inventory_id')->nullable()->constrained('inventories')->onDelete('cascade');
            $table->nullableMorphs('place');
            $table->foreignId('vendor_id')->nullable()->constrained('ledgers')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('cascade');
            $table->text('message_to_vendor')->nullable();
            $table->date('date')->nullable();
            $table->float('total')->default(0);
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_requisitions');
    }
};
