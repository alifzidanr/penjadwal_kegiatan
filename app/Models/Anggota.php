<?php
// app/Models/Anggota.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 't_anggota';
    protected $primaryKey = 'id_anggota';

    protected $fillable = [
        'nama_anggota',
        'email',
        'jabatan',
        'nomor_telepon',
        'id_unit_kerja'
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja', 'id_unit_kerja');
    }
}