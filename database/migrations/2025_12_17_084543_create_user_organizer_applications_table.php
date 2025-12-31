<?php

use App\Consts\AccountConst;
use App\Consts\TicketConst;
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
        Schema::create('user_organizer_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->unsignedTinyInteger('status');
            $table->string('event_description', TicketConst::EVENT_DESCRIPTION_LENGTH_MAX);
            $table->boolean('is_individual');
            $table->string('website_url', AccountConst::URL_LENGTH_MAX)->nullable();
            $table->dateTime('applied_at');
            $table->timestamps();
            $table->index('user_id');
            $table->index(['status', 'applied_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_organizer_applications');
    }
};
