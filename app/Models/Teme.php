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
        'tema_type',
        'specializare',
        'coordonator_id'
    ];

    public function coordinator()
    {
        return $this->belongsTo('App\Models\Coordonator');
    }
}
