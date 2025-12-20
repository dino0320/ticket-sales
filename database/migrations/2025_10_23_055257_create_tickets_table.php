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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_user_id')->references('id')->on('users');
            $table->string('event_title');
            $table->text('event_description')->nullable();
            $table->unsignedInteger('price');
            $table->string('stripe_price_id');
            $table->unsignedInteger('number_of_tickets');
            $table->unsignedInteger('number_of_reserved_tickets');
            $table->dateTime('event_start_date');
            $table->dateTime('event_end_date')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->timestamps();
            $table->index(['event_start_date', 'event_end_date']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
