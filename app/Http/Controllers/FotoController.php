<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Http\Request;

class FotoController extends Controller
{
    public function getFotos()
    {
        // Terminar...
    }

    public function getFoto($id)
    {
        // Terminar...
    }

    public function novaFoto(Request $request)
    {
        $ponto = auth()->user()->pontos()->find($request->ponto_id);
        $pescado = $ponto->pescados()->find($request->pescado_id);

        $foto = new Foto();
        $foto->pescado()->associate($pescado);
        $foto->path = $request->path;

        $foto->save();

        return response()->json($foto);
    }

    public function updateFoto(Request $request, $id)
    {
        // Terminar...
    }

    public function deleteFoto($id)
    {
        // Terminar...
    }
}
