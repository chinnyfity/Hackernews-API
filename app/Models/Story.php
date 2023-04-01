<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'descendants',
        'item_id',
        'kids',
        'score',
        'time',
        'title',
        'text',
        'url',
    ];

}
