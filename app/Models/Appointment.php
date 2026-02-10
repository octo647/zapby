<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'professional_id',
        'client_name',
        'client_whatsapp',
        'date',
        'time',
        'duration',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function getDateTimeAttribute(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time->format('H:i:s'));
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->format('Y-m-d'))
                     ->whereIn('status', ['pending', 'confirmed'])
                     ->orderBy('date')
                     ->orderBy('time');
    }

    public function scopeToday($query)
    {
        return $query->where('date', now()->format('Y-m-d'));
    }
}
