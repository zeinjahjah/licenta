<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Coordonator extends Model
{
    use HasFactory;
    protected $table = 'coordonatori';

    protected $fillable = [
        'user_id',
        'type',
        'phone',
        'address',
        'facultatea',
        'specializare',
        'is_admin',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
