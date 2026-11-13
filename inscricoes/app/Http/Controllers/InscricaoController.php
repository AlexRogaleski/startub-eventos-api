<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InscricaoController extends Controller
{
    public function index(Evento $evento)
    {
        return $evento->inscricoes()->orderBy('nome')->get();
    }

    public function store(Request $request, Evento $evento)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:120',
            'email' => [
                'required', 'email',
                Rule::unique('inscricoes')->where('evento_id', $evento->id),
            ],
            'cpf' => 'required|digits:11',
        ]);

        if ($evento->inscricoes()->count() >= $evento->vagas) {
            return response()->json(['message' => 'Não há vagas para este evento.'], 409);
        }

        $dados['status'] = $evento->valor > 0 ? 'pendente' : 'confirmada';

        $inscricao = $evento->inscricoes()->create($dados);

        return response()->json($inscricao, 201);
    }

    public function show(Inscricao $inscricao)
    {
        return $inscricao->load('evento');
    }
}
