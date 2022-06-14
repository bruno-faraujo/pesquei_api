<?php

namespace App\Http\Controllers;

use App\Models\Peixe;
use App\Models\Pescado;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PescadoController extends Controller
{
    public function getPescados($ponto_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 400);
        }

        return response()->json($ponto->pescados()->with('peixe')->get());
    }

    public function getPescado($ponto_id, $pescado_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->with('peixe')->findOrFail($pescado_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 400);
        }

        $result = collect([
            "id" => $pescado->id,
            "comprimento" => $pescado->comprimento,
            "peso" => $pescado->peso,
            "updated_at" => $pescado->updated_at,
            "peixe" => [
                "nome" => $pescado->peixe->nome,
                "nome_cientifico" => $pescado->peixe->nome_cientifico,
                "habitat" => $pescado->peixe->habitat
            ],
            "media" => [
                "url" => $pescado->getFirstMediaUrl(),
                 "thumb" => $pescado->getFirstMediaUrl('default', 'thumb')
            ]
        ]);


        return response()->json($result);

    }

    public function novoPescado(Request $request)
    {
        /*
        * Validação dos campos ponto_id e pescado_id
         */
        $request->validate([
            'ponto_id' => 'required|integer',
            'peixe_id' => 'required|integer',
            'comprimento' => 'integer',
            'peso' => 'integer',
            'foto' => 'mimes:jpeg,png|max:100000'
        ]);

        try {
            $ponto = auth()->user()->pontos()->findOrFail($request->ponto_id);
            $peixe = Peixe::findOrFail($request->peixe_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 400);
        }

        $pescado = new Pescado();
        $pescado->ponto()->associate($ponto);
        $pescado->peixe()->associate($peixe);
        $pescado->peso = $request->peso;
        $pescado->comprimento = $request->comprimento;
        $pescado->save();

        if ($request->has('foto')) {
            (new FotoController)->novaFoto($request, $pescado->id);
        }


        return response()->json(['message' => 'Peixe cadastrado com sucesso'], 201);
    }

    public function updatePescado(Request $request, $ponto_id, $pescado_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Ponto inválido'], 406);
        }
        $pescado->update($request->all());

        return response()->json($pescado, 200);
    }

    public function deletePescado($ponto_id, $pescado_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 406);
        }

        $pescado->delete();

        return response()->json(['message' => 'O pescado foi apagado com sucesso'], 200);
    }
}
