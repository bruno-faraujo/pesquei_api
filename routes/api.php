<?php

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

/**
 * Rotas protegidas pelo middleware Auth:sanctum
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('user', [AuthController::class, 'user']);

    Route::get('pontos', [PontoController::class, 'getPontos']);
    Route::get('ponto/{id}', [PontoController::class, 'getPonto']);
    Route::post('novo_ponto', [PontoController::class, 'novoPonto']);
    Route::post('update_ponto/{id}', [PontoController::class, 'updatePonto']);
    Route::post('delete_ponto/{id}', [PontoController::class, 'deletePonto']);

});
