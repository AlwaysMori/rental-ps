<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePsConsolesTable extends Migration
{
    public function up()
    {
        Schema::create('ps_consoles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['active', 'damaged']);
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->integer('timer')->nullable(); // Add this line to store the timer duration
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ps_consoles');
    }
}
