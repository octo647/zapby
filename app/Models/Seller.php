<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    protected $fillable = ['name', 'code', 'whatsapp', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function professionals(): HasMany
    {
        return $this->hasMany(Professional::class);
    }

    public static function normalizeCode(string $code): string
    {
        return strtoupper(trim($code));
    }
}
