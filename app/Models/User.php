<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, CanResetPassword;

    protected $fillable = ['name', 'email', 'password', 'google_id', 'role', 'plan_name', 'active', 'cpf_cnpj', 'asaas_customer_id'];

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

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
