<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Hjob extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'author',
        'item_id',
        'score',
        'text',
        'time',
        'title',
        'url',
    ];

}
