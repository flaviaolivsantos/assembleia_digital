<?php

namespace App\Http\Controllers;

use App\Models\Eleicao;
use App\Models\EleicaoCidade;
use App\Models\User;
use App\Models\Voto;
use Illuminate\Support\Facades\DB;

class ResultadoController extends Controller
{
    public function show(Eleicao $eleicao)
    {
        if ($eleicao->status !== 'encerrada') {
            abort(403, 'Resultados disponíveis apenas após o encerramento da eleição.');
        }

        $eleicao->load('cidades.cidade', 'perguntas.opcoes');

        $temVida    = $eleicao->perguntas->where('escopo', 'vida')->count() > 0;
        $temAlianca = $eleicao->perguntas->where('escopo', 'alianca')->count() > 0;

        $votosRaw             = $this->carregarVotos($eleicao);
        $votosPorMaquina      = $this->carregarVotosPorMaquina($eleicao);
        $votosPorCidade       = $this->carregarVotosPorCidade($eleicao);
        $vidaVotaramPorCidade = $this->carregarVidaVotaramPorCidade($eleicao);

        return view('admin.eleicoes.resultados', compact(
            'eleicao', 'votosRaw', 'votosPorMaquina', 'votosPorCidade',
            'vidaVotaramPorCidade', 'temVida', 'temAlianca'
        ));
    }

    public function ata(Eleicao $eleicao)
    {
        if ($eleicao->status !== 'encerrada') {
            abort(403, 'Ata disponível apenas após o encerramento da eleição.');
        }

        $eleicao->load('cidades.cidade', 'cidades.abertaPor', 'cidades.encerradaPor', 'perguntas.opcoes');

        $votosRaw = $this->carregarVotos($eleicao);

        return view('admin.eleicoes.ata', compact('eleicao', 'votosRaw'));
    }

    public function showResponsavel(EleicaoCidade $eleicaoCidade)
    {
        $eleicao = $eleicaoCidade->eleicao;

        if (!$eleicaoCidade->data_encerramento && $eleicao->status !== 'encerrada') {
            abort(403, 'Resultados disponíveis apenas após o encerramento da votação.');
        }

        $eleicao->load('perguntas.opcoes', 'cidades.cidade');
        $eleicaoCidade->load('cidade');

        $temVida    = $eleicao->perguntas->where('escopo', 'vida')->count() > 0;
        $temAlianca = $eleicao->perguntas->where('escopo', 'alianca')->count() > 0;

        $votosRaw               = $this->carregarVotos($eleicao);
        $todasCidades           = $eleicao->cidades;
        $votosPorMaquina        = $this->carregarVotosPorMaquina($eleicao);
        $votosPorCidade         = $this->carregarVotosPorCidade($eleicao);
        $vidaVotaramPorCidade   = $this->carregarVidaVotaramPorCidade($eleicao);

        return view('responsavel.resultados', compact(
            'eleicao', 'eleicaoCidade', 'votosRaw', 'todasCidades',
            'votosPorMaquina', 'votosPorCidade', 'vidaVotaramPorCidade',
            'temVida', 'temAlianca'
        ));
    }

    public function ataResponsavel(EleicaoCidade $eleicaoCidade)
    {
        $eleicao = $eleicaoCidade->eleicao;

        if (!$eleicaoCidade->data_encerramento && $eleicao->status !== 'encerrada') {
            abort(403, 'Ata disponível apenas após o encerramento da votação.');
        }

        $eleicao->load('perguntas.opcoes', 'cidades.cidade');
        $eleicaoCidade->load('cidade', 'abertaPor', 'encerradaPor');

        $votosRaw    = $this->carregarVotos($eleicao);
        $todasCidades = $eleicao->cidades;

        return view('responsavel.ata', compact('eleicao', 'eleicaoCidade', 'votosRaw', 'todasCidades'));
    }

    private function carregarVotos(Eleicao $eleicao)
    {
        return Voto::whereIn('pergunta_id', $eleicao->perguntas->pluck('id'))
            ->select('pergunta_id', 'opcao_id', DB::raw('count(*) as total'))
            ->groupBy('pergunta_id', 'opcao_id')
            ->get()
            ->keyBy(fn($v) => $v->pergunta_id . '_' . $v->opcao_id);
    }

    private function carregarVotosPorCidade(Eleicao $eleicao)
    {
        $perguntaIds = $eleicao->perguntas->pluck('id');

        // Votos remotos: cidade vem do token
        $remotos = Voto::whereIn('votos.pergunta_id', $perguntaIds)
            ->where('votos.origem', 'remoto')
            ->join('token_votacaos', 'votos.token_hash', '=', 'token_votacaos.token_hash')
            ->select('votos.pergunta_id', 'votos.opcao_id', 'token_votacaos.cidade_id', DB::raw('count(*) as total'))
            ->groupBy('votos.pergunta_id', 'votos.opcao_id', 'token_votacaos.cidade_id')
            ->get();

        // Votos presenciais: cidade vem do usuário (maquina)
        $presenciais = Voto::whereIn('votos.pergunta_id', $perguntaIds)
            ->where('votos.origem', 'presencial')
            ->whereNotNull('votos.maquina_id')
            ->join('users', 'votos.maquina_id', '=', 'users.id')
            ->select('votos.pergunta_id', 'votos.opcao_id', 'users.cidade_id', DB::raw('count(*) as total'))
            ->groupBy('votos.pergunta_id', 'votos.opcao_id', 'users.cidade_id')
            ->get();

        // Combinar em um mapa: "pergunta_opcao_cidade" => total
        $mapa = [];
        foreach ($remotos->concat($presenciais) as $row) {
            $key = "{$row->pergunta_id}_{$row->opcao_id}_{$row->cidade_id}";
            $mapa[$key] = ($mapa[$key] ?? 0) + $row->total;
        }

        return $mapa;
    }

    private function carregarVidaVotaramPorCidade(Eleicao $eleicao): array
    {
        $vidaIds = $eleicao->perguntas->where('escopo', 'vida')->pluck('id');
        if ($vidaIds->isEmpty()) return [];

        // Remote vida voters per city (via token_votacaos)
        $remote = Voto::whereIn('votos.pergunta_id', $vidaIds)
            ->where('votos.origem', 'remoto')
            ->join('token_votacaos', 'votos.token_hash', '=', 'token_votacaos.token_hash')
            ->select('token_votacaos.cidade_id', DB::raw('count(distinct votos.token_hash) as total'))
            ->groupBy('token_votacaos.cidade_id')
            ->get();

        // Presencial vida voters per city (via maquina user)
        $presencial = Voto::whereIn('votos.pergunta_id', $vidaIds)
            ->where('votos.origem', 'presencial')
            ->whereNotNull('votos.maquina_id')
            ->join('users', 'votos.maquina_id', '=', 'users.id')
            ->select('users.cidade_id', DB::raw('count(distinct votos.token_hash) as total'))
            ->groupBy('users.cidade_id')
            ->get();

        $mapa = [];
        foreach ($remote->concat($presencial) as $row) {
            $mapa[$row->cidade_id] = ($mapa[$row->cidade_id] ?? 0) + $row->total;
        }
        return $mapa;
    }

    private function carregarVotosPorMaquina(Eleicao $eleicao)
    {
        return Voto::whereIn('pergunta_id', $eleicao->perguntas->pluck('id'))
            ->where('origem', 'presencial')
            ->whereNotNull('maquina_id')
            ->select('maquina_id', DB::raw('count(distinct token_hash) as total_votos'))
            ->groupBy('maquina_id')
            ->get()
            ->map(function ($row) {
                $row->maquina = User::with('cidade')->select('id', 'nome', 'cidade_id')->find($row->maquina_id);
                return $row;
            })
            ->sortBy(fn($r) => $r->maquina?->cidade_id . $r->maquina?->nome);
    }
}
