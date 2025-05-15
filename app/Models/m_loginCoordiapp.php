<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Modelo para manejar las operaciones relacionadas con el inicio de sesión en CoordiApp.
 */
class m_loginCoordiapp extends Model
{
    /**
     * Inicia sesión para un usuario en CoordiApp.
     *
     * @param string $Usuario_App Nombre de usuario.
     * @param string $Estatus_Tecnico Estado del técnico (por defecto: 'activo').
     * @return \Illuminate\Support\Collection Colección con los datos del usuario.
     */
    public function iniciarSesion($Usuario_App, $Estatus_Tecnico = 'activo')
    {
        return DB::table('tecnicos')
            ->select('idTecnico', 'Nombre_T', 'Apellidos_T', 'Usuario_App', 'Estatus_Tecnico')
            ->where('Usuario_App', $Usuario_App)
            ->where('Estatus_Tecnico', $Estatus_Tecnico)
            ->first();
    }
}