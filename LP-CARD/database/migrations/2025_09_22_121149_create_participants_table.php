<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->text('creative_answer');
            $table->boolean('newsletter_subscription')->default(false);
            $table->string('week_identifier');
            $table->timestamps();

            $table->unique(['email', 'week_identifier']);
            $table->index('week_identifier');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('participants');
    }
};
