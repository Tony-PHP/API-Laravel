<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class m_materiales extends Model
{
    public function getMateriales($FK_Tecnico_Salida_Det)
    {
        return DB::table('salidas_contratistas')
            ->where('FK_Tecnico_Salida_Det', $FK_Tecnico_Salida_Det)
            ->get();
    }
}