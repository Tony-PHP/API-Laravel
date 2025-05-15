<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Modelo para manejar las operaciones relacionadas con los materiales.
 */
class m_materiales extends Model
{
    /**
     * Obtiene los materiales asociados a un técnico.
     *
     * @param int $FK_Tecnico_Salida_Det ID del técnico asociado a la salida de materiales.
     * @return \Illuminate\Support\Collection Colección con los materiales asociados.
     */
    public function getMateriales($FK_Tecnico_Salida_Det)
    {
        return DB::table('salidas_contratistas')
            ->select('Num_Serie_Salida_Det')
            ->where('FK_Tecnico_Salida_Det', $FK_Tecnico_Salida_Det)
            ->orderBy('Fecha_Salida', 'desc')
            ->get();
    }
}