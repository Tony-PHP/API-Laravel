<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class m_bolsaTac extends Model
{   
    public function getProduccionBolsaTac($Folio_Pisa)
    {
        try {
            $bolsaTac = DB::connection('mysql2')
                ->table('qm_tac_prod_bolsa')
                ->where('Folio_Pisa', $Folio_Pisa)
                ->get();

            return response()->json($bolsaTac);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
