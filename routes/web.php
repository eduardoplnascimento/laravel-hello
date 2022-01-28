<?php

use App\Http\Controllers\BillController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ClientController::class, 'index']);

Route::post('/clients/store', [ClientController::class, 'store']);
Route::get('/clients/show/{client}', [ClientController::class, 'show']);
Route::get('/clients/search/{text}', [ClientController::class, 'search']);
Route::get('/clients/order', [ClientController::class, 'order']);
Route::get('/clients/name/{name}', [ClientController::class, 'name']);
Route::get('/clients/bills/{client}', [ClientController::class, 'bills']);
Route::get('/bills/expensive/{value}', [BillController::class, 'expensive']);
Route::get('/bills/between/{value1}/{value2}', [BillController::class, 'between']);


Route::get('/soma/{num1}/{num2}', function ($num1, $num2) {
    logger()->info('Soma feita');
    return $num1 + $num2;
});

Route::get('/div/{num1}/{num2}', function ($num1, $num2) {
    if ($num2 == 0) {
        return logger()->error('Divisor zero!');
    }

    logger()->info('Div feita');
    return $num1 / $num2;
});

Route::get('/mult/{num1}/{num2}', function ($num1, $num2) {
    if ($num1 < 0 || $num2 < 0) {
        return logger()->warning('Negativo');
    }

    return $num1 * $num2;
});

Route::get('/sub/{num1}/{num2}', function ($num1, $num2) {
    logger()->debug('Sub feita', ['num1' => $num1, 'num2' => $num2, 'sub' => ($num1 - $num2)]);
    return $num1 - $num2;
});
