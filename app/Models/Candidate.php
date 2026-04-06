<?php

namespace App\Models;

use Database\Factories\CandidateFactory;
use App\Enums\Sex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'age',
        'sex',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'sex' => Sex::class,
        ];
        // (Se castea como $candidate->sex->label() :"Masculino/Femenino" o $candidate->sex->value :"M/F")
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
