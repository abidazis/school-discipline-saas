<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ViolationEvidence extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'violation_evidences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'violation_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the violation that owns the evidence.
     */
    public function violation(): BelongsTo
    {
        return $this->belongsTo(Violation::class);
    }

    /**
     * Get the file size in human-readable format.
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    /**
     * Get the file URL.
     */
    public function getUrlAttribute(): string
    {
        return route('violations.evidences.show', $this);
    }

    /**
     * Check if the file is an image.
     */
    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    /**
     * Delete the file from storage when model is deleted.
     */
    protected static function booted(): void
    {
        static::deleting(function (ViolationEvidence $evidence) {
            Storage::disk('public')->delete($evidence->file_path);
        });
    }
}
