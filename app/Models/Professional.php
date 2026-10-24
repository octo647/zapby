<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ProfessionalWorkPeriod;

class Professional extends Authenticatable
{
	use Notifiable;

	const SCHEDULE_WEEKLY = 'weekly';
    	const SCHEDULE_DATED  = 'dated';

    protected $fillable = [
        'name',
        'slug',
        'cpf',
        'whatsapp',
        'whatsapp_verified',
        'verification_code',
        'verification_code_expires_at',
        'profession',
        'services',
        'default_duration',
        'working_days',
        'working_hours',
	'subscription_status',
	'subscription_status',
        'trial_ends_at',
        'password',
        'access_key',
        'onboarding_completed',
        'is_admin',
        'seller_id',
        'schedule_mode',
    ];

    protected $casts = [
        'working_days' => 'array',
        'working_hours' => 'array',
        'services' => 'array',
        'trial_ends_at' => 'datetime',
        'whatsapp_verified' => 'boolean',
        'verification_code_expires_at' => 'datetime',
        'onboarding_completed' => 'boolean',
	'is_admin' => 'boolean',
	'schedule_mode' => 'string',
    ];
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public static function generateAccessKey(string $profession, int $id): string
    {
        return static::accessKeyPrefixForProfession($profession) . $id;
    }

    protected static function accessKeyPrefixForProfession(string $profession): string
    {
        $profession = mb_strtolower($profession);

        if (str_contains($profession, 'manicure')) {
            return 'man';
        }

        if (str_contains($profession, 'barbeiro') || str_contains($profession, 'barber')) {
            return 'barb';
        }

        if (str_contains($profession, 'advogado')) {
            return 'adv';
        }

        if (str_contains($profession, 'psic')) {
            return 'psi';
        }

        if (str_contains($profession, 'nutri')) {
            return 'nutri';
        }

        if (str_contains($profession, 'podo')) {
            return 'pod';
        }

        return 'pro';
    }

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];
    public function datePeriods(): HasMany
    {
        return $this->hasMany(ProfessionalDatePeriod::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function workPeriods(): HasMany
    {
    return $this->hasMany(ProfessionalWorkPeriod::class);
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->where('subscription_status', 'active')
                  ->orWhere(function (Builder $query) {
                      $query->where('subscription_status', 'trial')
                            ->where('trial_ends_at', '>', now());
                  });
        });
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

    /**
     * Verificar se o profissional configurou dias de trabalho
     */
    public function hasWorkingDays(): bool
    {
        return !empty($this->working_days) && is_array($this->working_days) && count($this->working_days) > 0;
    }

    public function hasRoutineConfigured(): bool
    {
        if ($this->usesDatedSchedule()) {
            return $this->datePeriods()->exists();
        }

        return $this->hasWorkingDays() || $this->workPeriods()->exists();
    }

    public function usesWeeklySchedule(): bool
    {
	return $this->schedule_mode === self::SCHEDULE_WEEKLY;
    }

    public function usesDatedSchedule(): bool
    {
    return $this->schedule_mode === self::SCHEDULE_DATED;
    }
    /**
     * Verificar se o profissional cadastrou pelo menos um serviço
     */
    public function hasServices(): bool
    {
        return !empty($this->services) && is_array($this->services) && count($this->services) > 0;
    }

    /**
     * Verificar se todas as configurações básicas foram feitas
     */
    public function hasCompletedBasicSettings(): bool
    {
        return $this->hasRoutineConfigured() && $this->hasServices();
    }

    /**
     * Obter os passos de onboarding pendentes
     */
    public function getPendingOnboardingSteps(): array
    {
        $steps = [];
        
        if (!$this->hasRoutineConfigured()) {
            $steps[] = [
                'id' => 'routine',
                'title' => 'Defina sua rotina de atendimento',
                'description' => 'Escolha agenda semanal ou por data e informe seus horários',
                'route' => 'professional.settings',
                'anchor' => '#agenda-settings',
                'icon' => '📅',
            ];
        }
        
        if (!$this->hasServices()) {
            $steps[] = [
                'id' => 'services',
                'title' => 'Cadastre pelo menos um serviço',
                'description' => 'Adicione os serviços que você oferece com preço e duração',
                'route' => 'professional.settings',
                'anchor' => '#services-settings',
                'icon' => '💼',
            ];
        }
        
        if ($this->hasCompletedBasicSettings() && !$this->onboarding_completed) {
            $steps[] = [
                'id' => 'share_link',
                'title' => 'Compartilhe seu link de agendamento',
                'description' => 'Divulgue seu link no WhatsApp, Facebook ou Instagram',
                'route' => 'professional.appointments',
                'anchor' => '#bookingLinkSection',
                'icon' => '📱',
            ];
        }
        
        return $steps;
    }

    /**
     * Marcar onboarding como completo manualmente
     */
    public function completeOnboarding(): void
    {
        $this->update(['onboarding_completed' => true]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
