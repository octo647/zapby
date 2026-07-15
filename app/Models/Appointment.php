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
        'phone_number',
        'service',
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

    public function getClientContactPhoneAttribute(): ?string
    {
        $phoneNumber = $this->normalizePhone($this->phone_number);

        if (!$phoneNumber) {
            $fallback = $this->normalizePhone($this->client_whatsapp);
            $phoneNumber = (strlen($fallback) >= 10 && strlen($fallback) <= 13) ? $fallback : null;
        }

        if (!$phoneNumber) {
            return null;
        }

        if (strlen($phoneNumber) === 10 || strlen($phoneNumber) === 11) {
            $phoneNumber = '55' . $phoneNumber;
        }

        return str_starts_with($phoneNumber, '55') && strlen($phoneNumber) >= 12 && strlen($phoneNumber) <= 13
            ? $phoneNumber
            : null;
    }

    protected function normalizePhone(?string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone ?? '');
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
