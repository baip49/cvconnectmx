<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CandidateDocument extends Model
{
    protected $fillable = [
        'candidate_id',
        'name',
        'file_path',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->slug)) {
                $document->slug = Str::slug($document->name).'-'.uniqid();
            }
        });

        static::deleting(function ($document) {
            if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
            }
        });
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
