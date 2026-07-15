<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalWorkPeriod extends Model
{
       protected $fillable = [
        'professional_id',
        'weekday',
        'start_time',
        'end_time',
    ];

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    } 
}
