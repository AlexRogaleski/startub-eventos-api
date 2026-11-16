<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Inscricao;
use App\Services\Asaas;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class InscricaoController extends Controller
{
    public function index(Evento $evento)
    {
        Gate::authorize('viewInscricoes', $evento);

        return $evento->inscricoes()->orderBy('nome')->get();
    }

    public function store(Request $request, Evento $evento, Asaas $asaas)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:120',
            'email' => [
                'required',
                'email',
                Rule::unique('inscricoes')->where('evento_id', $evento->id),
            ],
            'cpf' => 'required|digits:11',
        ]);

        if ($evento->inscricoes()->count() >= $evento->vagas) {
            return response()->json(['message' => 'Não há vagas para este evento.'], 409);
        }

        $dados['user_id'] = $request->user()->id;
        $dados['status'] = $evento->valor > 0 ? 'pendente' : 'confirmada';

        $inscricao = $evento->inscricoes()->create($dados);

        if ($evento->valor > 0) {
            try {
                $cliente = $asaas->criarCliente(
                    $inscricao->nome,
                    $inscricao->email,
                    $inscricao->cpf,
                );
                $cobranca = $asaas->criarCobranca(
                    $cliente['id'],
                    $evento->valor,
                    "Inscrição: {$evento->titulo}",
                    (string) $inscricao->id,
                );
            } catch (RequestException | ConnectionException $e) {
                report($e);
                
                $inscricao->delete();
                return response()->json([
                    'message' => 'Não foi possível gerar a cobrança. Tente novamente.',
                ], 502);
            }
            $inscricao->update([
                'asaas_cliente_id' => $cliente['id'],
                'asaas_cobranca_id' => $cobranca['id'],
                'link_pagamento' => $cobranca['invoiceUrl'],
            ]);
        }

        return response()->json($inscricao, 201);
    }

    public function show(Inscricao $inscricao)
    {
        Gate::authorize('view', $inscricao);

        return $inscricao->load('evento');
    }
}
