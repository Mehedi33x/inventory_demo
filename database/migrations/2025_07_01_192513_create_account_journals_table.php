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
        Schema::create('account_journals', function (Blueprint $table) {
            $table->id();
    $table->foreignId('sale_id')->constrained()->onDelete('cascade');
    $table->string('account'); // e.g. 'Sales Revenue', 'Cash', 'VAT Payable'
    $table->enum('type', ['debit', 'credit']);
    $table->decimal('amount', 10, 2);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_journals');
    }
};
