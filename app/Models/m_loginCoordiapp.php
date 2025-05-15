<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class m_loginCoordiapp extends Model
{
    public function iniciarSesion($Usuario_App,$Estatus_Tecnico = 'activo')
    {
        return DB::table('tecnicos')
            ->where('Usuario_App', $Usuario_App)
            ->where('Estatus_Tecnico', $Estatus_Tecnico)
            ->get();
    }
}