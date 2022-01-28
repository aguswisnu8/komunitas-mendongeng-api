<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUndangansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('undangans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('pengirim');
            $table->string('nm_kegiatan');
            $table->string('lokasi');
            $table->date('tgl');
            $table->longText('deskripsi');
            $table->enum('jenis', ['baksos', 'sekolah', 'korporat']);
            $table->string('penyelenggara');
            $table->string('contact');
            $table->enum('status', ['tunggu', 'terima', 'tolak'])->default('tunggu');
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
        Schema::dropIfExists('undangans');
    }
}
