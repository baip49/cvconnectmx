<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function affectedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affected_user_id');
    }

    public function incidentActions(): HasMany
    {
        return $this->hasMany(IncidentAction::class);
    }
}
