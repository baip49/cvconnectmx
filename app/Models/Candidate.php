<?php

namespace App\Models;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'last_name',
        'email',
        'phone',
        'age',
        'sex',
        'address'
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
