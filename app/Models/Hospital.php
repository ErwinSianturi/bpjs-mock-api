<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_rs',
        'tipe',
        'mitra_bpjs',
        'lokasi',
        'alamat',
        'no_telp',
        'deskripsi',
        'fasilitas',
        'jarak_estimasi',
    ];

    protected $casts = [
        'mitra_bpjs' => 'boolean',
        'fasilitas' => 'array',
    ];
}
