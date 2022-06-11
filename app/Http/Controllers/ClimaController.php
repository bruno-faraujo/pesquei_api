<?php

namespace App\Http\Controllers;

use App\Models\Clima;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClimaController extends Controller
{

    public function getClimaInfo(Request $request)
    {

        $climaInfoBd = Clima::
        where('estado', $request->estado)
            ->where('cidade', $request->cidade)
            ->latest()
            ->first();

        if (empty($climaInfoBd)) {

            // Cria uma nova entrada de clima no banco de dados

            $coordenadas = $this->getCoordenadas($request->cidade, $request->estado);
            $requestClima = $this->getClimaInfoFromApi($coordenadas['lat'], $coordenadas['lon']);
            $climaInfo = $this->saveNewClimaInfo($coordenadas['cidade'], $coordenadas['estado'], $requestClima);

            return $climaInfo;

        } else {

            $horaLeituraBd = Carbon::createFromTimestamp($climaInfoBd->dt);
            $agora = Carbon::now();

            if ($horaLeituraBd->diffInMinutes($agora) < 60) {
                // Diferença de tempo é menor que 60 minutos
                // Retorna a ocorrencia atual do banco de dados

                return $climaInfoBd;

            } else {
                // Diferença de tempo é maior que 60 minutos
                // Criar uma nova request a partir da API OpenWeatherMaps e SUBSTITUIR o valor salvo no banco de dados

                $requestCoordenadas = $this->getCoordenadas($climaInfoBd->cidade, $climaInfoBd->estado);
                $climaInfoFromApi = $this->getClimaInfoFromApi($requestCoordenadas['lat'], $requestCoordenadas['lon']);
                $newClimaInfo = $this->saveNewClimaInfo($requestCoordenadas['cidade'], $requestCoordenadas['estado'], $climaInfoFromApi);
                $climaInfoBd->delete();

                return $newClimaInfo;
            }
        }
    }


    private function saveNewClimaInfo($cidade, $estado, $climaInfoFromApi)
    {
        $climaInfo = new Clima();
        $climaInfo->cidade = $cidade;
        $climaInfo->estado = $estado;
        $climaInfo->dt = $climaInfoFromApi['dt'];

        if (isset($climaInfoFromApi['id'])) {
            $climaInfo->cidade_id = $climaInfoFromApi['id'];
        }
        if (isset($climaInfoFromApi['weather'][0]['id'])) {
            $climaInfo->weather_id = $climaInfoFromApi['weather'][0]['id'];
        }
        if (isset($climaInfoFromApi['weather'][0]['main'])) {
            $climaInfo->weather_main = $climaInfoFromApi['weather'][0]['main'];
        }
        if (isset($climaInfoFromApi['weather'][0]['description'])) {
            $climaInfo->weather_description = $climaInfoFromApi['weather'][0]['description'];
        }
        if (isset($climaInfoFromApi['weather'][0]['icon'])) {
            $climaInfo->weather_icon = $climaInfoFromApi['weather'][0]['icon'];
        }
        if (isset($climaInfoFromApi['main']['temp'])) {
            $climaInfo->main_temp = $climaInfoFromApi['main']['temp'];
        }
        if (isset($climaInfoFromApi['main']['feels_like'])) {
            $climaInfo->main_feels_like = $climaInfoFromApi['main']['feels_like'];
        }
        if (isset($climaInfoFromApi['main']['pressure'])) {
            $climaInfo->main_pressure = $climaInfoFromApi['main']['pressure'];
        }
        if (isset($climaInfoFromApi['main']['humidity'])) {
            $climaInfo->main_humidity = $climaInfoFromApi['main']['humidity'];
        }
        if (isset($climaInfoFromApi['visibility'])) {
            $climaInfo->visibility = $climaInfoFromApi['visibility'];
        }
        if (isset($climaInfoFromApi['wind']['speed'])) {
            $climaInfo->wind_speed = $climaInfoFromApi['wind']['speed'];
        }
        if (isset($climaInfoFromApi['wind']['deg'])) {
            $climaInfo->wind_deg = $climaInfoFromApi['wind']['deg'];
        }
        if (isset($climaInfoFromApi['wind']['gust'])) {
            $climaInfo->wind_gust = $climaInfoFromApi['wind']['gust'];
        }
        if (isset($climaInfoFromApi['clouds']['all'])) {
            $climaInfo->clouds_all = $climaInfoFromApi['clouds']['all'];
        }
        if (isset($climaInfoFromApi['rain']['1h'])) {
            $climaInfo->rain_1h = $climaInfoFromApi['rain']['1h'];
        }
        if (isset($climaInfoFromApi['rain']['3h'])) {
            $climaInfo->rain_3h = $climaInfoFromApi['rain']['3h'];
        }
        if (isset($climaInfoFromApi['snow']['1h'])) {
            $climaInfo->snow_1h = $climaInfoFromApi['snow']['1h'];
        }
        if (isset($climaInfoFromApi['snow']['3h'])) {
            $climaInfo->snow_3h = $climaInfoFromApi['snow']['3h'];
        }
        if (isset($climaInfoFromApi['sys']['sunrise'])) {
            $climaInfo->sys_sunrise = $climaInfoFromApi['sys']['sunrise'];
        }
        if (isset($climaInfoFromApi['sys']['sunset'])) {
            $climaInfo->sys_sunset = $climaInfoFromApi['sys']['sunset'];
        }

        $climaInfo->save();

        return $climaInfo;

    }

    private function getCoordenadas($cidade, $estado)
    {
        $key = env('OPENWEATHER_API_KEY');

        $coordenadasRequest = Http::get('http://api.openweathermap.org/geo/1.0/direct?q='
            . rawurlencode($cidade)
            . ','
            . rawurlencode($estado)
            . '&limit=1&appid='
            . $key);

        if ($coordenadasRequest->successful()) {

            $lat = $coordenadasRequest[0]['lat'];
            $lon = $coordenadasRequest[0]['lon'];

            $response = [
                'cidade' => $cidade,
                'estado' => $estado,
                'lat' => $lat,
                'lon' => $lon
            ];
            return $response;
        }
        return false;
    }

    public function requestCoordenadas(Request $request)
    {
        $cidade = $request->cidade;
        $estado = $request->estado;

        return response()->json($this->getCoordenadas($cidade, $estado));
    }

    private function getClimaInfoFromApi($lat, $lon)
    {
        $key = env('OPENWEATHER_API_KEY');

        $climaRequest = Http::get('http://api.openweathermap.org/data/2.5/weather?'
            . 'lat=' . $lat
            . '&lon=' . $lon
            . '&appid=' . $key
            . '&units=metric'
            . '&lang=pt_br');

        if ($climaRequest->successful()) {
            return $climaRequest;
        }
        return false;
    }

    public function getIconUrl(Request $request)
    {

        $icon = $request->icon;

        $baseIconUrl = 'https://openweathermap.org/img/wn/';

        $iconUrl = $baseIconUrl . $icon . '@4x.png';

        return $iconUrl;
    }
}
