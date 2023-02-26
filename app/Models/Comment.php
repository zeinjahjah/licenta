<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'commentari';

    use HasFactory;


    protected $fillable = [
        'event_id',
        'author_id',
        'author_type',
        'content'
    ];

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }
}
