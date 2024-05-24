<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    use HasFactory;
    protected $fillable = ['timezone','utc_offset'];


    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }
}
