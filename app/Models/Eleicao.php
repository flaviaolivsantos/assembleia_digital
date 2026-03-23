<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleicao extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'data_eleicao',
        'status',
        'aberta_vida',
        'data_abertura_vida',
        'data_encerramento_vida',
        'aberta_por_vida',
        'encerrada_por_vida',
    ];

    protected $casts = [
        'data_eleicao'           => 'date',
        'aberta_vida'            => 'boolean',
        'data_abertura_vida'     => 'datetime',
        'data_encerramento_vida' => 'datetime',
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

    public function abertaPorVida()
    {
        return $this->belongsTo(User::class, 'aberta_por_vida');
    }

    public function encerradaPorVida()
    {
        return $this->belongsTo(User::class, 'encerrada_por_vida');
    }

    public function estaAberta(): bool
    {
        return $this->status === 'aberta';
    }

    public function estaEncerrada(): bool
    {
        return $this->status === 'encerrada';
    }

    public function estaAbertaVida(): bool
    {
        return (bool) $this->aberta_vida;
    }

    public function temPerguntas(string $escopo): bool
    {
        return $this->perguntas()->where('escopo', $escopo)->exists();
    }
}
