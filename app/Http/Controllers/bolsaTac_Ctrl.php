<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_bolsaTac; // Importar el modelo

class bolsaTac_Ctrl extends Controller
{
    public function getProduccionBolsaTac($Folio_Pisa)
    {
        $bolsaTac = new m_bolsaTac();
        return $bolsaTac->getProduccionBolsaTac($Folio_Pisa);
    }
}
