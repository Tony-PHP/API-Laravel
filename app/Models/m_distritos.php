<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class m_distritos extends Model
{
    public function obtenerDistritos($idCope = null)
    {
        $query = DB::table('distritos')
            ->select('id_distrito', 'distrito', 'tipo_instalacion', 'fk_cope');

        if ($idCope) {
            $query->where('fk_cope', $idCope);
        }

        return $query->orderBy('distrito', 'asc')->get();
    }
    
    public function validarTipoDistrito($distrito)
    {
        return DB::table('distritos')
            ->select('tipo_instalacion')
            ->where('distrito', $distrito)
            ->orWhere('distrito', $distrito . 'FO')
            ->first();
    }
}