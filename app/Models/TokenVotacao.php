<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TokenVotacao extends Model
{
    use HasFactory;

    protected $fillable = ['token_hash', 'eleicao_id', 'cidade_id', 'usado'];

    protected $casts = ['usado' => 'boolean'];

    public function eleicao()
    {
        return $this->belongsTo(Eleicao::class);
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    public static function gerar(int $eleicaoId, int $cidadeId): array
    {
        $token = strtoupper(Str::random(5)) . '-' . strtoupper(Str::random(5)) . '-' . strtoupper(Str::random(5));
        $hash  = hash('sha256', $token);

        self::create([
            'token_hash' => $hash,
            'eleicao_id' => $eleicaoId,
            'cidade_id'  => $cidadeId,
            'usado'      => false,
        ]);

        return ['token' => $token, 'hash' => $hash];
    }
}
