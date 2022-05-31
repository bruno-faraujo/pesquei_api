<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function getListaSiglasUF()
    {
        $listaUfs = Municipio::select(['sigla_estado', 'estado'])->distinct('sigla_estado')->orderBy('sigla_estado')->get();

        return $listaUfs;
    }

    public function getListaCidades($uf)
    {
        $listaCidades = Municipio::select(['cidade', 'estado'])->where('sigla_estado', $uf)->orderBy('cidade')->get();

        return $listaCidades;
    }
}
