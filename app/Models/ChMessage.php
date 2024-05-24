<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Chatify\Traits\UUID;

class ChMessage extends Model
{
    // fillable
    protected $fillable = [
        'from_id',
        'to_id',
        'case_id',

     
        'type',
        'body',
        'attachment',
        'seen',
    ];
}
