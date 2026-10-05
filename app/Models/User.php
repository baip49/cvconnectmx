<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Filament\AvatarProviders\InitialsAvatarProvider;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    protected $fillable = [
        'email',
        'password',
        'uuid',
        'role_id',
        'name',
        'last_name',
        'avatar_path',
        'failed_login_attempts',
        'locked_until',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function candidate(): HasOne
    {
        return $this->hasOne(Candidate::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->withPivot(['type', 'granted_by']);
    }

    public function hasPermission(string $code): bool
    {
        $direct = $this->permissions()->where('code', $code)->first()?->pivot;

        if ($direct && $direct->type === 'denied') {
            return false;
        }

        if ($direct && $direct->type === 'granted') {
            return true;
        }

        return (bool) $this->role?->permissions()->where('code', $code)->exists();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function loginAttempts(): HasMany
    {
        return $this->hasMany(LoginAttempt::class);
    }

    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function reportedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function claimedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'claimed_by');
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isSupport(): bool
    {
        return $this->role?->name === 'support';
    }

    public function avatarColorHex(): string
    {
        $colors = InitialsAvatarProvider::COLORS;

        return '#'.$colors[abs(crc32((string) $this->name)) % count($colors)];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! filled($this->avatar_path)) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->avatar_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function systemAlerts(): HasMany
    {
        return $this->hasMany(SystemAlert::class);
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'user_trainings');
    }

    public function cvAccesses(): HasMany
    {
        return $this->hasMany(CvAccess::class, 'accessed_by');
    }
}
