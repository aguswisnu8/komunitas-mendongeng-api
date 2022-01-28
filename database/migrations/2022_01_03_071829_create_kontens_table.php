<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKontensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kontens', function (Blueprint $table) {
            $table->id();

            $table->string('judul');
            $table->string('gambar',2048);
            $table->string('link',2048)->nullable();
            $table->longText('deskripsi');
            $table->enum('jenis', ['artikel', 'video']);
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('user_id');

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
        Schema::dropIfExists('kontens');
    }
}
