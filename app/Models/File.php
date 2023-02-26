<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'attachement';

    use HasFactory;


    protected $fillable = [
        'event_id',
        'author_id',
        'author_type',
        'file_name',
        'file_path'

    ];

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }
}
