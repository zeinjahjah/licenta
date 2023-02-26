<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $table = 'workspace';

    use HasFactory;
    const CERE_IN_ASTEPTARE_STATUS = 0;
    const CERE_ACCEPTATA_STATUS = 1;
    const LUCRAREA_TERMINATA_STATUS = 2;
    const LUCRAREA_REFUSATA_STATUS = 3;

    protected $fillable = [
        'status',
        'tema_id',
        'student_id',
        'coordonator_id'
    ];
}
