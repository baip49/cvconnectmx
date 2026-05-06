<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_blocked' => 'boolean',
        'ai_rating' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function cvAccesses(): HasMany
    {
        return $this->hasMany(CvAccess::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CandidateDocument::class);
    }
}
