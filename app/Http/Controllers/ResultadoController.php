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

        if (!in_array($eleicao->status, ['aberta', 'encerrada'])) {
            abort(403, 'Resultados disponíveis apenas durante ou após a eleição.');
        }

        $filtro = request()->query('filtro', 'geral');
        if (!in_array($filtro, ['geral', 'alianca', 'vida'])) {
            $filtro = 'geral';
        }

        // Per-scope access guards
        if ($filtro === 'alianca' && !$eleicaoCidade->data_encerramento) {
            abort(403, 'Resultados de Aliança disponíveis apenas após o encerramento da votação de Aliança.');
        }
        if ($filtro === 'vida' && !$eleicao->data_encerramento_vida) {
            abort(403, 'Resultados de Vida disponíveis apenas após o encerramento da votação de Vida.');
        }
        if ($filtro === 'geral' && $eleicao->status !== 'encerrada') {
            // Auto-degrade to the scope that is closed
            if ($eleicaoCidade->data_encerramento && !$eleicao->data_encerramento_vida) {
                $filtro = 'alianca';
            } elseif (!$eleicaoCidade->data_encerramento && $eleicao->data_encerramento_vida) {
                $filtro = 'vida';
            } else {
                abort(403, 'Resultados gerais disponíveis apenas após o encerramento completo da eleição.');
            }
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
            'temVida', 'temAlianca', 'filtro'
        ));
    }

    public function ataResponsavel(EleicaoCidade $eleicaoCidade)
    {
        $eleicao = $eleicaoCidade->eleicao;

        $filtro = request()->query('filtro', 'geral');
        if (!in_array($filtro, ['geral', 'alianca', 'vida'])) {
            $filtro = 'geral';
        }

        if ($filtro === 'alianca' && !$eleicaoCidade->data_encerramento) {
            abort(403, 'Ata de Aliança disponível apenas após o encerramento da votação de Aliança.');
        }
        if ($filtro === 'vida' && !$eleicao->data_encerramento_vida) {
            abort(403, 'Ata de Vida disponível apenas após o encerramento da votação de Vida.');
        }
        if ($filtro === 'geral' && $eleicao->status !== 'encerrada'
            && (!$eleicaoCidade->data_encerramento || !$eleicao->data_encerramento_vida)) {
            abort(403, 'Ata geral disponível apenas após o encerramento completo da eleição.');
        }

        $eleicao->load('perguntas.opcoes', 'cidades.cidade', 'cidades.abertaPor', 'cidades.encerradaPor');
        $eleicaoCidade->load('cidade', 'abertaPor', 'encerradaPor');

        $temVida    = $eleicao->perguntas->where('escopo', 'vida')->count() > 0;
        $temAlianca = $eleicao->perguntas->where('escopo', 'alianca')->count() > 0;

        $votosRaw             = $this->carregarVotos($eleicao);
        $votosPorCidade       = $this->carregarVotosPorCidade($eleicao);
        $todasCidades         = $eleicao->cidades;
        $vidaVotaramPorCidade = $this->carregarVidaVotaramPorCidade($eleicao);

        return view('responsavel.ata', compact(
            'eleicao', 'eleicaoCidade', 'votosRaw', 'votosPorCidade', 'todasCidades',
            'temVida', 'temAlianca', 'vidaVotaramPorCidade', 'filtro'
        ));
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
