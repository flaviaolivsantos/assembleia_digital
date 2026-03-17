<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opcao extends Model
{
    use HasFactory;

    protected $fillable = ['pergunta_id', 'cidade_id', 'nome', 'foto', 'ordem'];

    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class);
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/sem-foto.png');
    }
}
