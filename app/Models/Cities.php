<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cities extends Model
{

    protected $guarded = [];
    protected $table = 'cities';


    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
