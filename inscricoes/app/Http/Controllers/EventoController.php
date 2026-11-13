<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Evento::orderBy('data')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:120',
            'descricao' => 'nullable|string',
            'local' => 'required|string|max:120',
            'data' => 'required|date|after:now',
            'vagas' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0',
        ]);

        $evento = Evento::create($dados);

        return response()->json($evento, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Evento $evento)
    {
        return $evento;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evento $evento)
    {
        $dados = $request->validate([
            'titulo' => 'sometimes|required|string|max:120',
            'descricao' => 'nullable|string',
            'local' => 'sometimes|required|string|max:120',
            'data' => 'sometimes|required|date|after:now',
            'vagas' => 'sometimes|required|integer|min:1',
            'valor' => 'sometimes|required|numeric|min:0',
        ]);
        
        $evento->update($dados);
       
        return $evento;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evento $evento)
    {
        $evento->delete();
        return response()->noContent();
    }
}
