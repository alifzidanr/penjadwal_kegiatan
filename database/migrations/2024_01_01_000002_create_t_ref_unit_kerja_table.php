<?php
// database/migrations/2024_01_01_000002_create_t_ref_unit_kerja_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_ref_unit_kerja', function (Blueprint $table) {
            $table->id('id_unit_kerja');
            $table->string('nama_unit_kerja');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_ref_unit_kerja');
    }
};