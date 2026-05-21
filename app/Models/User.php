<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'google_id', 'role', 'plan_name', 'active'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'active'   => 'boolean',
        ];
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
