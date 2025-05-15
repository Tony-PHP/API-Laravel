<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_loginCoordiapp;

class logincoordiappCtrl extends Controller
{
    protected $coordiApp;

    public function __construct(m_loginCoordiapp $coordiApp)
    {
        $this->coordiApp = $coordiApp;
    } 
    
    public function iniciarSesion($Usuario_App, $Estatus_Tecnico = 'activo')
    {
        try {
            $resultado = $this->coordiApp->iniciarSesion($Usuario_App, $Estatus_Tecnico);
            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
