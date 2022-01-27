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

