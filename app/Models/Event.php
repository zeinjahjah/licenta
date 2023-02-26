<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    use HasFactory;


    protected $fillable = [
        'workspace_id',
        'author_id',
        'author_type',
        'title',
        'type',
        'due_date',
        'descriere'
    ];

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function attachment()
    {
        return $this->hasOne('App\Models\File');
    }
}
