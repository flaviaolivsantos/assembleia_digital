<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEleicao extends Model
{
    public $timestamps = false;

    protected $fillable = ['eleicao_id', 'usuario_id', 'acao', 'descricao', 'created_at'];

    public function eleicao()
    {
        return $this->belongsTo(Eleicao::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function registrar(int $eleicaoId, string $acao, ?string $descricao = null): void
    {
        static::create([
            'eleicao_id' => $eleicaoId,
            'usuario_id' => auth()->id(),
            'acao'       => $acao,
            'descricao'  => $descricao,
            'created_at' => now(),
        ]);
    }
}
