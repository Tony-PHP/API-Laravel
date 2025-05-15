<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_materiales;

class materialesCtrl extends Controller
{
    protected $materiales;

    // Constructor corregido para inyectar el modelo m_materiales
    public function __construct(m_materiales $materiales)
    {
        $this->materiales = $materiales;
    }

    public function getOnt($FK_Tecnico_Salida_Det)
    {
        try {
            $resultado = $this->materiales->getMateriales($FK_Tecnico_Salida_Det);
            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}