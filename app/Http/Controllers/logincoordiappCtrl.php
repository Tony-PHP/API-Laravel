<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_loginCoordiapp;

/**
 * Controlador para manejar el inicio de sesión en CoordiApp.
 */
class logincoordiappCtrl extends Controller
{
    /**
     * @var m_loginCoordiapp
     */
    protected $coordiApp;

    /**
     * Constructor para inyectar el modelo m_loginCoordiapp.
     *
     * @param m_loginCoordiapp $coordiApp
     */
    public function __construct(m_loginCoordiapp $coordiApp)
    {
        $this->coordiApp = $coordiApp;
    }

    /**
     * Inicia sesión para un usuario en CoordiApp.
     *
     * @param string $Usuario_App Nombre de usuario.
     * @param string $Estatus_Tecnico Estado del técnico (por defecto: 'activo').
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con los datos del usuario o un mensaje de error.
     */
    public function iniciarSesion($Usuario_App, $Estatus_Tecnico = 'activo')
    {
        try {
            // Llamar al modelo para iniciar sesión
            $resultado = $this->coordiApp->iniciarSesion($Usuario_App, $Estatus_Tecnico);

            // Verificar si se encontraron resultados
            if (!$resultado) {
                return response()->json(['mensaje' => 'Usuario no encontrado o inactivo.'], 404);
            }

            return response()->json($resultado);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un error con código 500
            return response()->json(['error' => 'Error al iniciar sesión: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Valida si la sesión del usuario es válida.
     *
     * @param Request $request Solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON.
     */
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
            ], 401);
        }
    }

    /**
     * Cierra la sesión del usuario.
     *
     * @param Request $request Solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON.
     */
    public function cerrarSesion(Request $request)
    {
        $request->session()->flush();
        return response()->json(['mensaje' => 'Sesión cerrada correctamente.']);
    }
}