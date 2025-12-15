<?php

namespace App\Models;

class Template extends Model
{
    protected $table = 'templates';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'body_html',
        'variables',
        'is_system',
        'is_published',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_system' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function isSystem(): bool
    {
        return $this->is_system;
    }

    public function replaceVariables(array $data): string
    {
        $html = $this->body_html;
        foreach ($data as $key => $value) {
            $html = str_replace('{' . $key . '}', $value, $html);
        }
        return $html;
    }
}

