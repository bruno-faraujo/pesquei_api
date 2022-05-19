<?php

use App\Http\Controllers\FotoController;
use App\Http\Controllers\PescadoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PontoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 */

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('reset_password', [AuthController::class, 'resetPassword']);
Route::post('change_password', [AuthController::class, 'changePassword']);

/**
 * Rotas protegidas pelo middleware Auth:sanctum
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('user', [AuthController::class, 'user']);

    Route::get('pontos', [PontoController::class, 'getPontos']);
    Route::get('ultimo_ponto', [PontoController::class, 'ultimoPonto']);
    Route::get('ponto/{id}', [PontoController::class, 'getPonto']);
    Route::post('novo_ponto', [PontoController::class, 'novoPonto']);
    Route::post('update_ponto/{id}', [PontoController::class, 'updatePonto']);
    Route::post('delete_ponto/{id}', [PontoController::class, 'deletePonto']);

    Route::get('ponto/{ponto_id}/pescados', [PescadoController::class, 'getPescados']);
    Route::get('ponto/{ponto_id}/pescado/{pescado_id}', [PescadoController::class, 'getPescado']);
    Route::post('ponto/{ponto_id}/pescado_update/{pescado_id}', [PescadoController::class, 'updatePescado']);
    Route::post('ponto/{ponto_id}/pescado_delete/{pescado_id}', [PescadoController::class, 'deletePescado']);
    Route::post('novo_pescado', [PescadoController::class, 'novoPescado']);

    Route::get('ponto/{ponto_id}/pescado/{pescado_id}/fotos', [FotoController::class, 'getFotos']);
    Route::get('ponto/{ponto_id}/pescado/{pescado_id}/foto/{media_id}', [FotoController::class, 'getFoto']);
    Route::get('ponto/{ponto_id}/pescado/{pescado_id}/foto_delete/{media_id}', [FotoController::class, 'deleteFoto']);
    Route::post('ponto/{ponto_id}/pescado/{pescado_id}/foto_update/{media_id}', [FotoController::class, 'updateFoto']);
    Route::post('nova_foto', [FotoController::class, 'novaFoto']);

});
