<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['token_hash', 'pergunta_id', 'opcao_id', 'origem', 'maquina_id', 'created_at'];

    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class);
    }

    public function opcao()
    {
        return $this->belongsTo(Opcao::class);
    }

    public function maquina()
    {
        return $this->belongsTo(\App\Models\User::class, 'maquina_id');
    }
}
