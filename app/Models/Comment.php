<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'item_id',
        'kids',
        'parents',
        'text',
        'time',
    ];

}
