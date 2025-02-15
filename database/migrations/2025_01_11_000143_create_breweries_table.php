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
        Schema::create('breweries', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('brewery_type')->default('unknown')->index();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('address_3')->nullable();
            $table->string('city');
            $table->string('state_province');
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->string('website_url')->nullable();
            $table->string('phone')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breweries');
    }
};
