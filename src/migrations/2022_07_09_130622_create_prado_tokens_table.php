<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prado_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('pin', 6)->unique()->index();
            $table->string('hash', 160)->unique()->index();
            $table->string('token_id', 255)->nullable()->index();
            $table->string('blockchain', 36)->nullable()->index();
            $table->string('contract', 42)->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prado_tokens');
    }
};
