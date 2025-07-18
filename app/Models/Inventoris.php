<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Inventoris extends Model
{
    use HasFactory;

    protected $table = 'inventoris';
    protected $primaryKey = 'id';
    protected $fillable = [
        'uuid',
        'nama_barang',
        'kondisi',
        'jumlah',
        'tanggal_pembelian',
        'lokasi',
        'harga',
    ];

    protected static function boot()
    {
        parent::boot();

        // Event listener untuk membuat UUID sebelum menyimpan
        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
