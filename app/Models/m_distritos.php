<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Modelo para manejar las operaciones relacionadas con distritos.
 */
class m_distritos extends Model
{
    /**
     * Obtiene los distritos, opcionalmente filtrados por el ID de COPE.
     *
     * @param int|null $idCope ID de COPE para filtrar los distritos (opcional).
     * @return \Illuminate\Support\Collection Colección con los distritos.
     */
    public function obtenerDistritos($idCope = null)
    {
        $query = DB::table('distritos')
            ->select('id_distrito', 'distrito', 'tipo_instalacion', 'fk_cope');

        if (!is_null($idCope)) {
            $query->where('fk_cope', $idCope);
        }

        return $query->orderBy('distrito', 'asc')->get();
    }

    /**
     * Valida el tipo de instalación de un distrito.
     *
     * @param string $distrito Nombre del distrito a validar.
     * @return string|null Tipo de instalación del distrito o null si no se encuentra.
     */
    public function validarTipoDistrito($distrito)
    {
        $result = DB::table('distritos')
            ->select('tipo_instalacion')
            ->where('distrito', $distrito)
            ->orWhere('distrito', $distrito . 'FO')
            ->first();

        return $result ? $result->tipo_instalacion : null;
    }
}