<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Student extends Model
{
    protected $table = 'studenti';

    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'phone',
        'address',
        'facultatea',
        'specializare',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workspace()
    {
        return $this->hasOne('App\Models\Workspace');
    }

    public function studentName($id)
    {
        $student = self::find($id)->with('user')->first();
        $studentName = $student['user'] ? $student['user']['name'] : '';
        return $studentName;
    }
    
}
