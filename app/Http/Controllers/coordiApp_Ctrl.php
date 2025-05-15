<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\m_coordiApp;

class coordiApp_Ctrl extends Controller
{
    protected $coordiApp;

    public function __construct(m_coordiApp $coordiApp)
    {
        $this->coordiApp = $coordiApp;
    }

    public function getOrdenesCompletadas($FK_Tecnico_apps)
    {
        try {
            $ordenesCompletadas = $this->coordiApp->getOrdenesCompletadas($FK_Tecnico_apps);
            return response()->json($ordenesCompletadas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOrdenesIncompletas($FK_Tecnico_apps)
    {
        try {
            $ordenesIncompletadas = $this->coordiApp->getOrdenesIncompletadas($FK_Tecnico_apps);
            return response()->json($ordenesIncompletadas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}