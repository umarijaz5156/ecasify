<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Timezone;

class UserDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'mobile_number',
        'address',
        'city',
        'state',
        'zip_code',
        'landmark',
        'about',
    ];

    public static function getUserDetail($id)
    {
        $detail  = UserDetail::where('user_id', $id)->first();
        return $detail;
    }
    
    public static function getTimeZone($id)
    {
        return TimeZone::where('id', $id)->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timezoneTable()
    {
        return $this->belongsTo(Timezone::class, 'timezone');
    }
}
