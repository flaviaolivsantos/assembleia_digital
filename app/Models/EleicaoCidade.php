<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EleicaoCidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleicao_id',
        'cidade_id',
        'qtd_membros',
        'qtd_eleitorado',
        'qtd_vida',
        'qtd_presencial_vida',
        'votos_presenciais_vida',
        'qtd_presencial',
        'qtd_remoto',
        'votos_registrados',
        'votos_presenciais',
        'aberta',
        'data_abertura',
        'data_encerramento',
        'aberta_por',
        'encerrada_por',
    ];

    protected $casts = [
        'aberta'            => 'boolean',
        'data_abertura'     => 'datetime',
        'data_encerramento' => 'datetime',
    ];

    public function eleicao()
    {
        return $this->belongsTo(Eleicao::class);
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    public function abertaPor()
    {
        return $this->belongsTo(User::class, 'aberta_por');
    }

    public function encerradaPor()
    {
        return $this->belongsTo(User::class, 'encerrada_por');
    }
}
