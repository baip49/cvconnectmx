<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvAccess extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function accessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accessed_by');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
