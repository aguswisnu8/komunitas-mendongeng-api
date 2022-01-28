<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('level', ['anggota', 'admin'])->after('email')->default('anggota');
            $table->integer('active')->after('email')->default(1);
            $table->string('alamat')->after('email')->nullable();
            $table->string('medsos')->after('email')->nullable();
            $table->string('deskripsi')->after('email')->nullable();
            $table->integer('exp')->after('email')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('level', ['anggota', 'admin']);
            $table->dropColumn('active');
            $table->dropColumn('alamat');
            $table->dropColumn('medsos');
            $table->dropColumn('deskripsi');
            $table->dropColumn('exp');
        });
    }
}
