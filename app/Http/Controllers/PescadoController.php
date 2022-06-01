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

        return response()->json($ponto->pescados()->get());
    }

    public function getPescado($ponto_id, $pescado_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 400);
        }

        return response()->json($pescado);

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
            $pescado = $ponto->pescado()->findOrFail($pescado_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 406);
        }

        // Apaga as fotos
        foreach ($pescado->getMedia()->all() as $foto)
        {
            $foto->delete();
        }

        // Apaga o pescado
        $pescado->delete();

        return response()->json(['message' => 'O pescado foi apagado com sucesso'], 200);
    }
}
