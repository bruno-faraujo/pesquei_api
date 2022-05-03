<?php

namespace App\Http\Controllers;

use App\Models\Peixe;
use App\Models\Pescado;
use App\Models\Ponto;
use Illuminate\Http\Request;

class PescadoController extends Controller
{
    public function novoPescado(Request $request)
    {
        $ponto = auth()->user()->pontos()->find($request->ponto_id);
        $peixe = Peixe::find($request->peixe_id);

        $pescado = new Pescado();

        $pescado->ponto()->associate($ponto);
        $pescado->peixe()->associate($peixe);
        $pescado->save();

        return response()->json($pescado);
    }
}
