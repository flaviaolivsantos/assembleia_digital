<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TokenVotacao extends Model
{
    use HasFactory;

    protected $fillable = ['token_hash', 'eleicao_id', 'cidade_id', 'usado', 'escopo'];

    protected $casts = ['usado' => 'boolean'];

    public function eleicao()
    {
        return $this->belongsTo(Eleicao::class);
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    public static function gerar(int $eleicaoId, int $cidadeId, string $escopo = 'alianca'): array
    {
        do {
            $token = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $hash  = hash('sha256', $token);
        } while (self::where('token_hash', $hash)->where('usado', false)->exists());

        self::create([
            'token_hash' => $hash,
            'eleicao_id' => $eleicaoId,
            'cidade_id'  => $cidadeId,
            'usado'      => false,
            'escopo'     => $escopo,
        ]);

        return ['token' => $token, 'hash' => $hash];
    }
}
