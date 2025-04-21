<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimerToPsConsolesTable extends Migration
{
    public function up()
    {
        Schema::table('ps_consoles', function (Blueprint $table) {
            $table->integer('timer')->nullable()->after('room_id'); // Tambahkan kolom timer
        });
    }

    public function down()
    {
        Schema::table('ps_consoles', function (Blueprint $table) {
            $table->dropColumn('timer'); // Hapus kolom timer jika rollback
        });
    }
}
