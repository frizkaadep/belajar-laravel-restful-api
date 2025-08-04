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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street', 255)->nullable(false);
            $table->string('city', 100)->nullable(false);
            $table->string('province', 100)->nullable(false);
            $table->string('country', 100)->nullable(false);
            $table->string('postal_code', 20)->nullable(false);
            $table->unsignedBigInteger('contact_id')->nullable(false);
            $table->foreign('contact_id')
                ->on('contacts')->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
