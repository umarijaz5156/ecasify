<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CalendarEvent extends Model
{
    use HasFactory;

    // protected $table = 'calendar_events';

    protected $fillable = [
        'title', 'start_time', 'end_time', 'meeting_link', 'case_id', 'user_id', 'location',
    ];
}
