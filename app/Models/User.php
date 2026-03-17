<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'perfil',
        'cidade_id',
        'acesso_ate',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password'   => 'hashed',
        'acesso_ate' => 'datetime',
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    public function isAdmin(): bool
    {
        return $this->perfil === 'admin';
    }

    public function isResponsavel(): bool
    {
        return $this->perfil === 'responsavel';
    }

    public function isMesario(): bool
    {
        return $this->perfil === 'mesario';
    }

    public function isMaquina(): bool
    {
        return $this->perfil === 'maquina';
    }
}
