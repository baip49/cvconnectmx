<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    use HasFactory;

    protected $table = 'work_experiencies';

    protected $guarded = [];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
