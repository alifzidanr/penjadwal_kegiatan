<?php
// database/migrations/2024_01_01_000003_create_t_detail_kegiatan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_detail_kegiatan', function (Blueprint $table) {
            $table->id('id_kegiatan');
            $table->string('nama_kegiatan');
            $table->string('person_in_charge')->nullable();
            $table->text('anggota')->nullable();
            $table->date('tanggal')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('nama_tempat')->nullable();
            $table->foreignId('id_unit_kerja')->nullable()->constrained('t_ref_unit_kerja', 'id_unit_kerja');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_detail_kegiatan');
    }
};