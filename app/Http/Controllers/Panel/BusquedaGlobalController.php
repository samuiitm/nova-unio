<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\BuscadorGlobalPanelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusquedaGlobalController extends Controller
{
    public function __construct(
        protected BuscadorGlobalPanelService $buscadorGlobalPanelService,
    ) {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        return view('panel.busqueda.index', [
            'q' => $q,
            'resultados' => $this->buscadorGlobalPanelService->buscar($q, $request->user(), false),
        ]);
    }

    public function sugerencias(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        return response()->json(
            $this->buscadorGlobalPanelService->buscar($q, $request->user(), true)
        );
    }
}