<?php

namespace App\Models;

use App\Support\Hash;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isGuest(): bool
    {
        return $this->role === 'guest';
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function templates()
    {
        return $this->hasMany(Template::class);
    }

    public function files()
    {
        return $this->hasMany(ProposalFile::class);
    }
}

