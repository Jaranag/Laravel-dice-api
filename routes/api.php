<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiceRollController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/players', [UserController::class, 'register'])->name('players.register');
Route::post('/players/login', [UserController::class, 'login'])->name('login');

// Route::middleware('auth:api')->get('/all', UserController::all());
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/players', [UserController::class, 'index'], ['middleware' => ['role:admin']])->name('players.showAll');
    Route::put('/players/{id}', [UserController::class, 'update'])->name('players.update');
    Route::get('/players/{id}/games', [DiceRollController::class, 'index'])->name('diceroll.index');
    Route::post('/players/{id}/games', [DiceRollController::class, 'roll'])->name('diceroll.roll');
    Route::delete('/players/{id}/games', [DiceRollController::class, 'delete'])->name('diceroll.delete');
    Route::get('/players/ranking', [UserController::class, 'ranking'])->name('users.ranking');
    Route::get('/players/ranking/winner', [UserController::class, 'winner'])->name('users.winner');
    Route::get('/players/ranking/loser', [UserController::class, 'loser'])->name('users.loser');
});
