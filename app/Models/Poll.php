<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Poll extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'author',
        'descendants',
        'item_id',
        'kids',
        'parts',
        'score',
        'text',
        'time',
        'title',
    ];

}
