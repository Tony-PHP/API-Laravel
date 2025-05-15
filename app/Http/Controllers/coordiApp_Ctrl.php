<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_coordiApp;

/**
 * Controlador para manejar las operaciones relacionadas con CoordiApp.
 */
class coordiApp_Ctrl extends Controller
{
    /**
     * @var m_coordiApp
     */
    protected $coordiApp;

    /**
     * Constructor para inyectar el modelo m_coordiApp.
     *
     * @param m_coordiApp $coordiApp
     */
    public function __construct(m_coordiApp $coordiApp)
    {
        $this->coordiApp = $coordiApp;
    }

    /**
     * Obtiene las órdenes completadas de un técnico.
     *
     * @param int $FK_Tecnico_apps ID del técnico.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON.
     */
    public function getOrdenesCompletadas($FK_Tecnico_apps)
    {
        try {
            $ordenesCompletadas = $this->coordiApp->getOrdenesCompletadas($FK_Tecnico_apps);
            return response()->json($ordenesCompletadas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene las órdenes incompletas de un técnico.
     *
     * @param int $FK_Tecnico_apps ID del técnico.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON.
     */
    public function getOrdenesIncompletas($FK_Tecnico_apps)
    {
        try {
            $ordenesIncompletadas = $this->coordiApp->getOrdenesIncompletadas($FK_Tecnico_apps);
            return response()->json($ordenesIncompletadas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene opciones basadas en el paso y parámetros proporcionados.
     *
     * @param Request $request Solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON.
     */
    public function obtenerOpciones(Request $request)
    {
        $step = $request->query('step');
        $bindings = [];

        if ($step === '5m' && $request->query('idEstado')) {
            $bindings['idEstado'] = $request->query('idEstado');
        } elseif ($step === '5c' && $request->query('idMunicipio')) {
            $bindings['idMunicipio'] = $request->query('idMunicipio');
        } elseif ($step === '6' && $request->query('idTecnico')) {
            $bindings['idTecnico'] = $request->query('idTecnico');
        } elseif (in_array($step, ['5m', '5c', '6'])) {
            return response()->json(['mensaje' => 'Parámetro requerido no proporcionado'], 400);
        }

        try {
            $result = $this->coordiApp->obtenerOpciones($step, $bindings);
            return response()->json($result ?: ['mensaje' => 'No se pudo obtener los datos']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['mensaje' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene una orden específica basada en el Folio Pisa.
     *
     * @param string $Folio_Pisa Folio de Pisa.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON.
     */
    public function getOrden($Folio_Pisa)
    {
        try {
            $orden = $this->coordiApp->getOrden($Folio_Pisa);
            return response()->json($orden);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza un registro en CoordiApp.
     *
     * @param Request $request Solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON.
     */
    public function actualizar(Request $request)
    {
        $validatedData = $request->validate([
            'idtecnico_instalaciones_coordiapp' => 'required|integer',
            'FK_Cope' => 'nullable|string',
            'Foto_Ont' => 'nullable|string',
            'Fecha_Coordiapp' => 'nullable|date',
            'Foto_Casa_Cliente' => 'nullable|string',
            'Foto_INE' => 'nullable|string',
            'FK_Tecnico_apps' => 'nullable|integer',
            'No_Serie_ONT' => 'nullable|string',
            'Distrito' => 'nullable|string',
            'Puerto' => 'nullable|string',
            'Terminal' => 'nullable|string',
            'Tipo_Tarea' => 'nullable|string',
            'Estatus_Orden' => 'nullable|string',
            'Foto_Puerto' => 'nullable|string',
            'Metraje' => 'nullable|numeric',
            'Tecnologia' => 'nullable|string',
        ]);

        $id = $validatedData['idtecnico_instalaciones_coordiapp'];

        try {
            $folioPisa = $this->coordiApp->obtenerFolioPisa($id);
            if (!$folioPisa) {
                return response()->json(['mensaje' => 'No se encontró el Folio_Pisa para el ID proporcionado.'], 404);
            }

            $allowedFields = [
                'FK_Cope', 'Foto_Ont', 'Fecha_Coordiapp', 'Foto_Casa_Cliente', 'Foto_INE',
                'FK_Tecnico_apps', 'No_Serie_ONT', 'Distrito', 'Puerto', 'Terminal',
                'Tipo_Tarea', 'Estatus_Orden', 'Foto_Puerto', 'Metraje', 'Tecnologia',
            ];

            $imageFields = [
                'Foto_Ont' => 'fotoONT',
                'Foto_Casa_Cliente' => 'foto_casa_cliente',
                'Foto_INE' => 'foto_INE',
                'Foto_Puerto' => 'foto_puerto',
            ];

            $this->coordiApp->actualizarRegistro($id, $validatedData, $allowedFields, $imageFields, $folioPisa);

            return response()->json(['mensaje' => 'Registro actualizado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene una comparativa basada en los parámetros proporcionados.
     *
     * @param Request $request Solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON.
     */
    public function comparativa(Request $request)
    {
        $validatedData = $request->validate([
            'anio' => 'required|integer',
            'mes' => 'required|integer|min:1|max:12',
            'idTecnico' => 'required|integer',
            'opcion' => 'required|integer|in:1,2'
        ]);

        try {
            $result = $this->coordiApp->obtenerComparativa(
                $validatedData['anio'],
                $validatedData['mes'],
                $validatedData['idTecnico'],
                $validatedData['opcion']
            );

            return response()->json($result ?: ['mensaje' => 'No se encontraron resultados.']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['mensaje' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}