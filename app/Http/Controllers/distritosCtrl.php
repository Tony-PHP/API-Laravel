<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_distritos;

/**
 * Controlador para manejar las operaciones relacionadas con distritos.
 */
class distritosCtrl extends Controller
{
    /**
     * @var m_distritos
     */
    protected $distritos;

    /**
     * Constructor para inyectar el modelo m_distritos.
     *
     * @param m_distritos $distritos
     */
    public function __construct(m_distritos $distritos)
    {
        $this->distritos = $distritos;
    }

    /**
     * Obtiene los distritos, opcionalmente filtrados por el ID de COPE.
     *
     * @param Request $request Solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con los distritos.
     */
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

    /**
     * Valida el tipo de instalación de un distrito.
     *
     * @param Request $request Solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con el tipo de instalación.
     */
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
                return response()->json(['tipo_instalacion' => $result]);
            } else {
                return response()->json(['tipo_instalacion' => 'SIN INFO']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}