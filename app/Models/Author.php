<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Author extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'name',
        'karma',
        'about',
        'delay',
        'created',
    ];

}
