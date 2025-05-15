<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_coordiApp;

class coordiApp_Ctrl extends Controller
{
    protected $coordiApp;

    public function __construct(m_coordiApp $coordiApp)
    {
        $this->coordiApp = $coordiApp;
    }

    public function getOrdenesCompletadas($FK_Tecnico_apps)
    {
        try {
            $ordenesCompletadas = $this->coordiApp->getOrdenesCompletadas($FK_Tecnico_apps);
            return response()->json($ordenesCompletadas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOrdenesIncompletas($FK_Tecnico_apps)
    {
        try {
            $ordenesIncompletadas = $this->coordiApp->getOrdenesIncompletadas($FK_Tecnico_apps);
            return response()->json($ordenesIncompletadas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function obtenerOpciones(Request $request)
    {
        $step = $request->query('step'); // Obtener el parámetro 'step' de la consulta
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

    public function getOrden($Folio_Pisa)
    {
        try {
            $ordenesCompletadas = $this->coordiApp->getOrden($Folio_Pisa);
            return response()->json($ordenesCompletadas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function actualizar(Request $request)
    {
        // Validar los datos de entrada
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
            // Agregar más campos según sea necesario
        ]);

        $id = $validatedData['idtecnico_instalaciones_coordiapp'];

        try {
            // Obtener el Folio_Pisa
            $folioPisa = $this->coordiApp->obtenerFolioPisa($id);
            if (!$folioPisa) {
                return response()->json(['mensaje' => 'No se encontró el Folio_Pisa para el ID proporcionado.'], 404);
            }

            // Campos permitidos
            $allowedFields = [
                'FK_Cope' => 'FK_Cope',
                'Foto_Ont' => 'Foto_Ont',
                'Fecha_Coordiapp' => 'Fecha_Coordiapp',
                'Foto_Casa_Cliente' => 'Foto_Casa_Cliente',
                'Foto_INE' => 'Foto_INE',
                'FK_Tecnico_apps' => 'FK_Tecnico_apps',
                'No_Serie_ONT' => 'No_Serie_ONT',
                'Distrito' => 'Distrito',
                'Puerto' => 'Puerto',
                'Terminal' => 'Terminal',
                'Tipo_Tarea' => 'Tipo_Tarea',
                'Estatus_Orden' => 'Estatus_Orden',
                'Foto_Puerto' => 'Foto_Puerto',
                'Metraje' => 'Metraje',
                'Tecnologia' => 'Tecnologia',
            ];

            // Campos de imágenes
            $imageFields = [
                'Foto_Ont' => 'fotoONT',
                'Foto_Casa_Cliente' => 'foto_casa_cliente',
                'Foto_INE' => 'foto_INE',
                'Foto_Puerto' => 'foto_puerto',
            ];

            // Actualizar el registro
            $this->coordiApp->actualizarRegistro($id, $validatedData, $allowedFields, $imageFields, $folioPisa);

            return response()->json(['mensaje' => 'Registro actualizado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => $e->getMessage()], 500);
        }
    }

    public function verificarSesion(Request $request)
    {
        if ($request->session()->has('user')) {
            return response()->json([
                'mensaje' => 'Sesión válida',
                'usuario' => $request->session()->get('user')
            ]);
        } else {
            return response()->json([
                'mensaje' => 'Sesión no válida o expirada'
            ], 401); // Código de estado 401 para sesión no válida
        }
    }

    public function cerrarSesion(Request $request)
    {
        // Limpiar todos los datos de la sesión
        $request->session()->flush();

        return response()->json(['mensaje' => 'Sesión cerrada correctamente.']);
    }

    public function comparativa(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'anio' => 'required|integer',
            'mes' => 'required|integer|min:1|max:12',
            'idTecnico' => 'required|integer',
            'opcion' => 'required|integer|in:1,2'
        ]);

        try {
            $anio = $validatedData['anio'];
            $mes = $validatedData['mes'];
            $idTecnico = $validatedData['idTecnico'];
            $opcion = $validatedData['opcion'];

            // Obtener los datos desde el modelo
            $result = $this->coordiApp->obtenerComparativa($anio, $mes, $idTecnico, $opcion);

            return response()->json($result ?: ['mensaje' => 'No se encontraron resultados.']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['mensaje' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}