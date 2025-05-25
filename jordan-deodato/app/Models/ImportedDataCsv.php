<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedDataCsv extends Model
{
    use HasFactory;

    protected $table = 'imported_data_csvs';

    protected $fillable = [
        'data',
        'temperatura'
    ];

    protected $casts = [
        'data' => 'datetime'
    ];
}
