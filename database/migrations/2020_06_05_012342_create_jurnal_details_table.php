<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perkiraan');
            $table->integer('jumlah')->unsigned();
            $table->foreignId('jurnal_id');
            $table->enum('tipe', array('D', 'K'));
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('perkiraan')->references('id')->on('perkiraans');
            $table->foreign('jurnal_id')->references('id')->on('jurnals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jurnal_details');
    }
}
