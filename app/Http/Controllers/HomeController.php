<?php

namespace App\Http\Controllers;

use App\Models\Peixe;
use App\Models\Pescado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function getGallery()
    {

        $gallery = DB::table('media')->latest('updated_at')->take(12)->get();

        $collection = collect();

        foreach ($gallery as $item) {

            $pescado = Pescado::find($item->model_id);
            $ponto = $pescado->ponto;
            $peixe = $pescado->peixe;
            $pescador = $pescado->ponto->user;

            $urlImg = $pescado->getMedia()->find($item->id)->getUrl();
            $urlThumb = $pescado->getMedia()->find($item->id)->getUrl('thumb');
            $dataImg = $pescado->getMedia()->find($item->id)->updated_at;

            $collection->push([
                'media' => [
                    'id' => $item->id,
                    'urlImg' => $urlImg,
                    'urlThumb' => $urlThumb,
                    'dataImg' => $dataImg
                ],
                'dados' => [
                    'pescador' => $pescador->name,
                    'peixe' => $peixe->nome,
                    'comprimento' => $pescado->comprimento,
                    'peso' => $pescado->peso,
                    'ponto' => $ponto->nome
                ]
            ]);
        }

        return response()->json($collection);

    }

    public function getPeixesRankingResumo()
    {

        $peixes = Peixe::withCount('pescados')->get();

        $collection = collect();

        foreach ($peixes as $peixe) {

            if ($peixe->pescados->count() > 0) {
                $collection->push([
                    'id' => $peixe->id,
                    'nome' => $peixe->nome,
                    'contagem' => $peixe->pescados_count
                ]);
            }
        }

        return response()->json($collection->sortByDesc('contagem')->values()->take(6));
    }


    public function getCondicoesTempo(Request $request)
    {
        $key = env('OPENWEATHER_API_KEY');

        $coordenadas = Http::get('http://api.openweathermap.org/geo/1.0/direct?q='
            .rawurlencode($request->cidade)
            .','
            .rawurlencode($request->estado)
            .'&limit=1&appid='
            .$key);

        $lat = $coordenadas[0]['lat'];
        $lon = $coordenadas[0]['lon'];

        $apiRequest = Http::get('http://api.openweathermap.org/data/2.5/weather?'
        .'lat='.$lat
        .'&lon='.$lon
        .'&appid='.$key
        .'&units=metric'
        .'&lang=pt_br');


/*        $response = [

        ];

        $baseIconUrl = 'http://openweathermap.org/img/wn/';

        // http://openweathermap.org/img/wn/10d@2x.png

        if ($apiRequest['weather']['id'][0] === 2) {
            $iconUrl = $baseIconUrl.$apiRequest['weather']['icon'].'@2x.png';
        }
        if ($apiRequest['weather']['id'][0] === 3) {
            $iconUrl = $baseIconUrl.$apiRequest['weather']['icon'].'@3x.png';
        }
        if ($apiRequest['weather']['id'][0] === 5) {
            $iconUrl = $baseIconUrl.$apiRequest['weather']['icon'].'@5x.png';
        }
        if ($apiRequest['weather']['id'][0] === 6) {
            $iconUrl = $baseIconUrl.$apiRequest['weather']['icon'].'@6x.png';
        }
        if ($apiRequest['weather']['id'][0] === 7) {
            $iconUrl = $baseIconUrl.$apiRequest['weather']['icon'].'@7x.png';
        }
        if ($apiRequest['weather']['id'][0] === 8) {
            $iconUrl = $baseIconUrl.$apiRequest['weather']['icon'].'@8x.png';
        }*/

        return $apiRequest->json();
        //return $apiRequest['weather'];

    }


}
