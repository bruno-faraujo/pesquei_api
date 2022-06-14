<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PontoController extends Controller
{
    /**
     * Retorna todos os pontos de um determinado usuário
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPontos()
    {

        $pontos = auth()->user()->pontos()->orderBy('nome')->get();

        if (empty($pontos)) {
            return response()->json(['message' => 'Nenhum ponto de pesca cadastrado.'], 400);
        }

        return response()->json($pontos, 200);
    }

    /**
     * Retorna o ponto mais recente (último)
     * @return \Illuminate\Http\JsonResponse
     */
    public function ultimoPonto()
    {
        try {
            $ultimoPonto = auth()->user()->ultimoPonto()->firstOrFail();
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Ponto inválido'], 406);
        }
        return response()->json($ultimoPonto, 200);
    }

    /**
     * Retorna um ponto do usuário a partir do id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPonto($id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Ponto inválido'], 406);
        }

        return response()->json($ponto, 200);

    }

    /**
     * Cria um novo ponto
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function novoPonto(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        /*
         * Checa se já existe um ponto com as mesmas coordenadas
         */
        try {
            auth()->user()->pontos()->where('latitude', $request->latitude)->where('longitude', $request->longitude)->firstOrFail();
                }
                catch (ModelNotFoundException)
                {
                    $ponto = new Ponto();
                    $ponto->nome = $request->nome;
                    $ponto->latitude = $request->latitude;
                    $ponto->longitude = $request->longitude;
                    auth()->user()->pontos()->save($ponto);

                    return response()->json(['message' => "O novo ponto de pesca foi cadastrado com sucesso."], 201);
                }

                return response()->json(['message' => 'Já existe um ponto cadastrado com as coordenadas informadas'], 406);
    }

    /**
     * Atualiza as informações de um ponto existente
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePonto(Request $request, $id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Ponto inválido'], 406);
        }
        $ponto->update($request->all());

        return response()->json(["message" => "As alterações foram salvas com sucesso."], 200);
    }

    /**
     * Localiza e apaga um ponto a partir do id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePonto($id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Ponto inválido'], 406);
        }

        foreach ($ponto->pescados()->get() as $pescado)
        {
            $pescado->delete();
        }
        // Finalmente apaga o ponto
        $ponto->delete();

        return response()->json(['message' => 'O ponto foi apagado com sucesso'], 200);
    }

}
