<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Modelo para manejar las operaciones relacionadas con la producción de Bolsa TAC.
 */
class m_bolsaTac extends Model
{
    /**
     * Obtiene la producción de Bolsa TAC basada en el Folio Pisa.
     *
     * @param string $Folio_Pisa El folio de Pisa para buscar la producción.
     * @return \Illuminate\Support\Collection Colección con los datos de la producción.
     * @throws \Exception Si ocurre un error durante la consulta.
     */
    public function getProduccionBolsaTac($Folio_Pisa)
    {
        return DB::connection('mysql2')
            ->table('qm_tac_prod_bolsa')
            ->where('Folio_Pisa', $Folio_Pisa)
            ->get();
    }
}