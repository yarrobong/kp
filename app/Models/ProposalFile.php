<?php

namespace App\Models;

class ProposalFile extends Model
{
    protected $table = 'proposal_files';

    protected $fillable = [
        'proposal_id',
        'user_id',
        'type',
        'original_name',
        'path',
        'mime_type',
        'size',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isLogo(): bool
    {
        return $this->type === 'logo';
    }
}

