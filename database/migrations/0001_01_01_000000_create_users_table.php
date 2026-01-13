<?php

use App\Consts\AccountConst;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', AccountConst::NAME_LENGTH_MAX);
            $table->string('email', AccountConst::EMAIL_LENGTH_MAX)->unique();
            $table->string('password', AccountConst::PASSWORD_LENGTH_MAX);
            $table->boolean('is_organizer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
