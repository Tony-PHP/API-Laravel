<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_materiales;

/**
 * Controlador para manejar las operaciones relacionadas con materiales.
 */
class materialesCtrl extends Controller
{
    /**
     * @var m_materiales
     */
    protected $materiales;

    /**
     * Constructor para inyectar el modelo m_materiales.
     *
     * @param m_materiales $materiales
     */
    public function __construct(m_materiales $materiales)
    {
        $this->materiales = $materiales;
    }

    /**
     * Obtiene los materiales asociados a un técnico.
     *
     * @param int $FK_Tecnico_Salida_Det ID del técnico asociado a la salida de materiales.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con los materiales o un mensaje de error.
     */
    public function getOnt($FK_Tecnico_Salida_Det)
    {
        try {
            // Llamar al modelo para obtener los materiales
            $resultado = $this->materiales->getMateriales($FK_Tecnico_Salida_Det);

            // Verificar si se encontraron materiales
            if (!$resultado || $resultado->isEmpty()) {
                return response()->json(['mensaje' => 'No se encontraron materiales para el técnico proporcionado.'], 404);
            }

            return response()->json($resultado);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un error con código 500
            return response()->json(['error' => 'Error al obtener los materiales: ' . $e->getMessage()], 500);
        }
    }
}