<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('transaksi_id');
            $table->foreignId('perkiraan1_id');
            $table->foreignId('perkiraan2_id');
            $table->foreignId('user_id');
            $table->string('keterangan');
            $table->integer('jumlah');
            $table->timestamps();
            $table->softDeletes();


            /** FOREIGN KEY */
            $table->foreign('transaksi_id')->references('id')->on('jenis_transaksis');
            $table->foreign('perkiraan1_id')->references('id')->on('perkiraans');
            $table->foreign('perkiraan2_id')->references('id')->on('perkiraans');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jurnals');
    }
}
