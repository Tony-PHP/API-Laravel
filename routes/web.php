<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bolsaTac_Ctrl;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bolsa-tac/{Folio_Pisa}', [bolsaTac_Ctrl::class, 'getProduccionBolsaTac']);