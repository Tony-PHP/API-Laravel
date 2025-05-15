<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bolsaTac_Ctrl;
use App\Http\Controllers\coordiApp_Ctrl;
use App\Http\Controllers\logincoordiappCtrl;
use App\Http\Controllers\materialesCtrl;
use App\Http\Controllers\distritosCtrl;

// Ruta principal
Route::get('/', function () {
    return view('welcome');
});

// Grupo de rutas para Bolsa TAC
Route::prefix('bolsa-tac')->group(function () {
    Route::get('/{Folio_Pisa}', [bolsaTac_Ctrl::class, 'getProduccionBolsaTac'])->name('bolsa-tac.produccion');
});

// Grupo de rutas para CoordiApp
Route::prefix('coordiapp')->group(function () {
    Route::get('/completadas-tecnico/{FK_Tecnico_apps}', [coordiApp_Ctrl::class, 'getOrdenesCompletadas'])->name('coordiapp.completadas');
    Route::get('/incompletas-tecnico/{FK_Tecnico_apps}', [coordiApp_Ctrl::class, 'getOrdenesIncompletas'])->name('coordiapp.incompletas');
    Route::get('/opciones', [coordiApp_Ctrl::class, 'obtenerOpciones'])->name('coordiapp.opciones');
    Route::get('/get-orden/{Folio_Pisa}', [coordiApp_Ctrl::class, 'getOrden'])->name('coordiapp.get-orden');
    Route::put('/actualizar', [coordiApp_Ctrl::class, 'actualizar'])->name('coordiapp.actualizar');
    Route::post('/comparativa', [coordiApp_Ctrl::class, 'comparativa'])->name('coordiapp.comparativa');
});

// Ruta para iniciar sesión en CoordiApp
Route::get('/iniciar-sesion/{Usuario_App}', [logincoordiappCtrl::class, 'iniciarSesion'])->name('coordiapp.iniciar-sesion');

// Grupo de rutas para Materiales
Route::prefix('materiales')->group(function () {
    Route::get('/{FK_Tecnico_Salida_Det}', [materialesCtrl::class, 'getOnt'])->name('materiales.obtener');
});

// Grupo de rutas para Distritos
Route::prefix('distritos')->group(function () {
    // Ruta para obtener distritos
    Route::post('/obtener', [distritosCtrl::class, 'obtenerDistritos'])->name('distritos.obtener');

    // Ruta para validar el tipo de distrito
    Route::post('/validar-tipo', [distritosCtrl::class, 'validarTipoDistrito'])->name('distritos.validar-tipo');
});