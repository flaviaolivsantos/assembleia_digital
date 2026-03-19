<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presenca extends Model
{
    use HasFactory;

    protected $fillable = ['eleicao_id', 'cidade_id', 'nome', 'token', 'votou'];

    protected $casts = ['votou' => 'boolean'];

    public function eleicao()
    {
        return $this->belongsTo(Eleicao::class);
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }
}
