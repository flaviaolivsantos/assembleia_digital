<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleicao extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'data_eleicao', 'status'];

    protected $casts = [
        'data_eleicao' => 'date',
    ];

    public function cidades()
    {
        return $this->hasMany(EleicaoCidade::class);
    }

    public function perguntas()
    {
        return $this->hasMany(Pergunta::class);
    }

    public function logs()
    {
        return $this->hasMany(LogEleicao::class);
    }

    public function estaAberta(): bool
    {
        return $this->status === 'aberta';
    }

    public function estaEncerrada(): bool
    {
        return $this->status === 'encerrada';
    }
}
