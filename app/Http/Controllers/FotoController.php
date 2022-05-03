<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FotoController extends Controller
{
    public function getFotos($ponto_id, $pescado_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->findOrFail($pescado_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['error' => 'Requisição inválida'], 400);
        }

        return response()->json($pescado->fotos()->get());
    }

    public function getFoto($ponto_id, $pescado_id, $foto_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
            $foto = $pescado->fotos()->findOrFail($foto_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['error' => 'Requisição inválida'], 400);
        }

        return response()->json($foto);
    }

    public function novaFoto(Request $request)
    {
        /*
        * Validação dos campos ponto_id e pescado_id
         */
        $request->validate([
            'ponto_id' => 'required|integer',
            'pescado_id' => 'required|integer',
            'path' => 'required|string'
        ]);

        try {
            $ponto = auth()->user()->pontos()->findOrFail($request->ponto_id);
            $pescado = $ponto->pescados()->findOrFail($request->pescado_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['error' => 'Requisição inválida'], 400);
        }

        $foto = new Foto();
        $foto->pescado()->associate($pescado);
        $foto->path = $request->path;
        $foto->save();

        return response()->json(['message' => 'Foto cadastrada com sucesso'], 201);
    }

    public function updateFoto($ponto_id, $pescado_id, $foto_id, Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
            $foto = $pescado->fotos()->findOrFail($foto_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['error' => 'Requisição inválida'], 406);
        }
        $foto->update($request->all());

        return response()->json($foto, 200);
    }

    public function deleteFoto($ponto_id, $pescado_id, $foto_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescado()->findOrFail($pescado_id);
            $foto = $pescado->fotos()->findOrFail($foto_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['error' => 'Requisição inválida'], 406);
        }
        $foto->delete();

        return response()->json(['message' => 'A foto foi apagada com sucesso'], 200);
    }
}
