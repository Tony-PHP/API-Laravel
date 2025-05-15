<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class m_coordiApp extends Model
{
    public function getOrdenesCompletadas($FK_Tecnico_apps)
    {
        return DB::table('View_Detalle_Coordiapp_Completadas')
            ->where('FK_Tecnico_apps', $FK_Tecnico_apps)
            ->get();
    }

    public function getOrdenesIncompletadas($FK_Tecnico_apps)
    {
        return DB::table('View_Detalle_Coordiapp_Incompletas')
            ->where('FK_Tecnico_apps', $FK_Tecnico_apps)
            ->get();
    }
}