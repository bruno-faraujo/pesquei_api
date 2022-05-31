<?php

namespace App\Http\Controllers;

use App\Models\Peixe;
use Illuminate\Http\Request;

class PeixeController extends Controller
{
    public function getPeixes()
    {
        $peixes = Peixe::all()->sortBy(['nome']);
        return response()->json($peixes);
    }

    public function getPeixe($id)
    {
        // Terminar...
    }

    public function novoPeixe(Request $request)
    {
        // Terminar...
    }

    public function updatePeixe(Request $request, $id)
    {
        // Terminar...
    }

    public function deletePeixe($id)
    {
        // Terminar...
    }

}
