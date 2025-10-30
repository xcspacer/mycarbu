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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('station');
            $table->decimal('goa_quantity', 8, 3)->default(0);
            $table->decimal('goa_used', 8, 3)->default(0);
            $table->decimal('goa_discount_per_liter', 8, 5)->default(0);
            $table->decimal('goa_plus_discount_per_liter', 8, 5)->default(0);
            $table->decimal('sp95_quantity', 8, 3)->default(0);
            $table->decimal('sp95_used', 8, 3)->default(0);
            $table->decimal('sp95_discount_per_liter', 8, 5)->default(0);
            $table->decimal('sp95_plus_discount_per_liter', 8, 5)->default(0);
            $table->decimal('sp98_quantity', 8, 3)->default(0);
            $table->decimal('sp98_used', 8, 3)->default(0);
            $table->decimal('sp98_discount_per_liter', 8, 5)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'station']);
            $table->index(['start_date', 'end_date']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};