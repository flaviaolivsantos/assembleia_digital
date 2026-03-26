<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    public $timestamps = false;

    protected $fillable = ['usuario_id', 'acao', 'descricao', 'ip', 'created_at'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function registrar(string $acao, ?string $descricao = null): void
    {
        static::create([
            'usuario_id' => auth()->id(),
            'acao'       => $acao,
            'descricao'  => $descricao,
            'ip'         => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
