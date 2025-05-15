<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_bolsaTac;

/**
 * Controlador para manejar las operaciones relacionadas con la producción de Bolsa TAC.
 */
class bolsaTac_Ctrl extends Controller
{
    /**
     * @var m_bolsaTac
     */
    protected $bolsaTac;

    /**
     * Constructor para inyectar el modelo m_bolsaTac.
     *
     * @param m_bolsaTac $bolsaTac
     */
    public function __construct(m_bolsaTac $bolsaTac)
    {
        $this->bolsaTac = $bolsaTac;
    }

    /**
     * Obtiene la producción de Bolsa TAC basada en el Folio Pisa.
     *
     * @param string $Folio_Pisa El folio de Pisa para buscar la producción.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con los datos de la producción.
     */
    public function getProduccionBolsaTac($Folio_Pisa)
    {
        try {
            $result = $this->bolsaTac->getProduccionBolsaTac($Folio_Pisa);

            if ($result->isEmpty()) {
                return response()->json(['mensaje' => 'No se encontraron datos para el Folio Pisa proporcionado.'], 404);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la producción: ' . $e->getMessage()], 500);
        }
    }
}