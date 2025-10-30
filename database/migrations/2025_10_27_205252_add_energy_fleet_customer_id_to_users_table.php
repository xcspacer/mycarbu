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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('energy_fleet_customer_id')->nullable()->after('role');
            $table->boolean('is_active_from_energy')->default(true)->after('energy_fleet_customer_id');
            $table->index('energy_fleet_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['energy_fleet_customer_id']);
            $table->dropColumn(['energy_fleet_customer_id', 'is_active_from_energy']);
        });
    }
};
