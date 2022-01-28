<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMendongengsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mendongengs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lokasi');
            $table->date('tgl');
            $table->longText('deskripsi');
            $table->string('gambar', 2048)->nullable();
            $table->string('partner');
            $table->enum('jenis', ['baksos', 'sekolah', 'korporat']);
            $table->tinyInteger('status')->default(1);
            $table->string('gmap_link', 2048)->nullable();
            $table->bigInteger('udangan_id')->nullable();
            $table->integer('exp_req');
            $table->integer('st_req');
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
        Schema::dropIfExists('mendongengs');
    }
}
