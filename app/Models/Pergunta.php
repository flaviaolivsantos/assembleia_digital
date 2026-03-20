<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pergunta extends Model
{
    use HasFactory;

    protected $fillable = ['eleicao_id', 'pergunta', 'qtd_respostas', 'escopo', 'ordem'];

    public function eleicao()
    {
        return $this->belongsTo(Eleicao::class);
    }

    public function opcoes()
    {
        return $this->hasMany(Opcao::class)->orderBy('nome');
    }

    public function opcoesPorCidade(int $cidadeId)
    {
        if ($this->escopo === 'vida') {
            return $this->opcoes();
        }
        return $this->opcoes()->where('cidade_id', $cidadeId);
    }
}
