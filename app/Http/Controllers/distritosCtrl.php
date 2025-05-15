<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_distritos;

class distritosCtrl extends Controller
{
    protected $distritos;

    public function __construct(m_distritos $distritos)
    {
        $this->distritos = $distritos;
    }

    public function obtenerDistritos(Request $request)
    {
        try {
            // Obtener el parámetro opcional 'id_cope' del cuerpo de la solicitud
            $idCope = $request->input('id_cope');

            // Llamar al modelo para obtener los distritos
            $result = $this->distritos->obtenerDistritos($idCope);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function validarTipoDistrito(Request $request)
    {
        // Validar el parámetro de entrada
        $validatedData = $request->validate([
            'distrito' => 'required|string'
        ]);

        try {
            $distrito = $validatedData['distrito'];

            // Llamar al modelo para validar el tipo de distrito
            $result = $this->distritos->validarTipoDistrito($distrito);

            if ($result) {
                return response()->json(['tipo_instalacion' => $result->tipo_instalacion]);
            } else {
                return response()->json(['tipo_instalacion' => 'SIN INFO']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}