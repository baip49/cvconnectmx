<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function targetRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'target_role_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_trainings');
    }

    public function userTrainings(): HasMany
    {
        return $this->hasMany(UserTraining::class);
    }
}
