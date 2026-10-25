<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CandidateDocument extends Model
{
    /**
     * MIME types candidates may upload as supporting documents.
     *
     * @var list<string>
     */
    public const ACCEPTED_FILE_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

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
            foreach (['s3', 'local'] as $disk) {
                if ($document->file_path && Storage::disk($disk)->exists($document->file_path)) {
                    Storage::disk($disk)->delete($document->file_path);
                }
            }
        });
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
