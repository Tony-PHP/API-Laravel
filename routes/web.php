<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bolsaTac_Ctrl;
use App\Http\Controllers\coordiApp_Ctrl;
use App\Http\Controllers\logincoordiappCtrl;
use App\Http\Controllers\materialesCtrl;
use App\Http\Controllers\distritosCtrl;

/**
 * Rutas principales de la API.
 */

// Ruta principal
Route::get('/', function () {
    return view('welcome');
})->name('home');

/**
 * Grupo de rutas para Bolsa TAC.
 */
Route::prefix('bolsa-tac')->group(function () {
    // Ruta para obtener la producción de Bolsa TAC por Folio Pisa
    Route::get('/{Folio_Pisa}', [bolsaTac_Ctrl::class, 'getProduccionBolsaTac'])->name('bolsa-tac.produccion');
});

/**
 * Grupo de rutas para el manejo de inicio de sesión en CoordiApp.
 */
Route::prefix('login-coordiapp')->group(function () {
    // Ruta para iniciar sesión
    Route::get('/iniciar-sesion/{Usuario_App}/{Estatus_Tecnico?}', [logincoordiappCtrl::class, 'iniciarSesion'])->name('login-coordiapp.iniciar-sesion');

    // Ruta para verificar si la sesión es válida
    Route::get('/verificar-sesion', [logincoordiappCtrl::class, 'verificarSesion'])->name('login-coordiapp.verificar-sesion');

    // Ruta para cerrar sesión
    Route::post('/cerrar-sesion', [logincoordiappCtrl::class, 'cerrarSesion'])->name('login-coordiapp.cerrar-sesion');
});

/**
 * Grupo de rutas para CoordiApp.
 */
Route::prefix('coordiapp')->group(function () {
    // Ruta para obtener órdenes completadas por técnico
    Route::get('/completadas-tecnico/{FK_Tecnico_apps}', [coordiApp_Ctrl::class, 'getOrdenesCompletadas'])->name('coordiapp.completadas');

    // Ruta para obtener órdenes incompletas por técnico
    Route::get('/incompletas-tecnico/{FK_Tecnico_apps}', [coordiApp_Ctrl::class, 'getOrdenesIncompletas'])->name('coordiapp.incompletas');

    // Ruta para obtener opciones basadas en parámetros
    Route::get('/opciones', [coordiApp_Ctrl::class, 'obtenerOpciones'])->name('coordiapp.opciones');

    // Ruta para obtener una orden específica por Folio Pisa
    Route::get('/get-orden/{Folio_Pisa}', [coordiApp_Ctrl::class, 'getOrden'])->name('coordiapp.get-orden');

    // Ruta para actualizar un registro en CoordiApp
    Route::put('/actualizar', [coordiApp_Ctrl::class, 'actualizar'])->name('coordiapp.actualizar');

    // Ruta para obtener una comparativa de producción
    Route::post('/comparativa', [coordiApp_Ctrl::class, 'comparativa'])->name('coordiapp.comparativa');
});

/**
 * Grupo de rutas para Materiales.
 */
Route::prefix('materiales')->group(function () {
    // Ruta para obtener materiales asociados a un técnico
    Route::get('/{FK_Tecnico_Salida_Det}', [materialesCtrl::class, 'getOnt'])->name('materiales.obtener');
});

/**
 * Grupo de rutas para Distritos.
 */
Route::prefix('distritos')->group(function () {
    // Ruta para obtener distritos, opcionalmente filtrados por ID de COPE
    Route::post('/obtener', [distritosCtrl::class, 'obtenerDistritos'])->name('distritos.obtener');

    // Ruta para validar el tipo de instalación de un distrito
    Route::post('/validar-tipo', [distritosCtrl::class, 'validarTipoDistrito'])->name('distritos.validar-tipo');
});