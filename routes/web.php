<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bolsaTac_Ctrl;
use App\Http\Controllers\coordiApp_Ctrl;

Route::get('/', function () {
    return view('welcome');
});

//RUTA PARA OBTENER ORDENES DE BOLSA TAC
Route::get('/bolsa-tac/{Folio_Pisa}', [bolsaTac_Ctrl::class, 'getProduccionBolsaTac']);

//RUTA PARA OBTENER ORDENES COMPLETADAS COORDIAPP TECNICO
Route::get('/completadas-tecnico/{FK_Tecnico_apps}', [coordiApp_Ctrl::class, 'getOrdenesCompletadas']);

//RUTA PARA OBTENER ORDENES INCOMPLETAS COORDIAPP TECNICO
Route::get('/incompletas-tecnico/{FK_Tecnico_apps}', [coordiApp_Ctrl::class, 'getOrdenesIncompletas']);