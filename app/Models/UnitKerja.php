<?php
// app/Models/UnitKerja.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    protected $table = 't_ref_unit_kerja';
    protected $primaryKey = 'id_unit_kerja';

    protected $fillable = [
        'nama_unit_kerja'
    ];

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'id_unit_kerja', 'id_unit_kerja');
    }
}