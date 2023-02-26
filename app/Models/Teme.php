<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teme extends Model
{
    use HasFactory;
    protected $table = 'teme_de_licenta';
    protected $fillable = [
        'title',
        'detalii',
        'specializare',
        'coordonator_id'
    ];
}
