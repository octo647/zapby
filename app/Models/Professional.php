<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Professional extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'slug',
        'whatsapp',
        'whatsapp_verified',
        'verification_code',
        'verification_code_expires_at',
        'profession',
        'default_duration',
        'working_days',
        'working_hours',
        'subscription_status',
        'trial_ends_at',
        'password',
    ];

    protected $casts = [
        'working_days' => 'array',
        'working_hours' => 'array',
        'trial_ends_at' => 'datetime',
        'whatsapp_verified' => 'boolean',
        'verification_code_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function timeBlocks(): HasMany
    {
        return $this->hasMany(TimeBlock::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function isActive(): bool
    {
        return $this->subscription_status === 'active' || $this->isOnTrial();
    }

    public function generateVerificationCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);
        
        return $code;
    }

    public function verifyCode(string $code): bool
    {
        if (!$this->verification_code || !$this->verification_code_expires_at) {
            return false;
        }
        
        if ($this->verification_code_expires_at->isPast()) {
            return false;
        }
        
        if ($this->verification_code !== $code) {
            return false;
        }
        
        $this->update([
            'whatsapp_verified' => true,
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);
        
        return true;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
