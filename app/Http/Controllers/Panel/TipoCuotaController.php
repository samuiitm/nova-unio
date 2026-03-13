<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTipoCuotaRequest;
use App\Http\Requests\UpdateTipoCuotaRequest;
use App\Models\TipoCuota;
use Illuminate\Http\Request;

class TipoCuotaController extends Controller
{
    public function index(Request $request)
    {
        $editId = (string) $request->query('edit', '');
        $edit = $editId ? TipoCuota::find($editId) : null;

        $tipos = TipoCuota::orderByDesc('activo')
            ->orderBy('nombre')
            ->get();

        return view('panel.pagos.tipos', compact('tipos', 'edit'));
    }

    public function store(StoreTipoCuotaRequest $request)
    {
        $data = $this->normalizarDatos($request->validated());

        TipoCuota::create($data);

        return back()->with('ok', 'Tipo de cuota creado.');
    }

    public function update(UpdateTipoCuotaRequest $request, TipoCuota $tipoCuota)
    {
        $data = $this->normalizarDatos($request->validated());

        $tipoCuota->update($data);

        return redirect()->route('panel.pagos.tipos')->with('ok', 'Tipo de cuota actualizado.');
    }

    public function destroy(TipoCuota $tipoCuota)
    {
        $tieneCuotas = $tipoCuota->cuotas()->exists();

        if ($tieneCuotas) {
            $tipoCuota->update(['activo' => false]);
            return back()->with('ok', 'No se puede borrar: tiene cuotas. Se ha desactivado.');
        }

        $tipoCuota->delete();

        return back()->with('ok', 'Tipo de cuota borrado.');
    }

    private function normalizarDatos(array $data): array
    {
        $data['activo'] = (bool) ($data['activo'] ?? false);

        if (($data['tipo_vigencia'] ?? 'meses') === 'temporada') {
            $data['duracion_meses'] = 1;
            $data['venta_inicio_mes'] = (int) ($data['venta_inicio_mes'] ?? 8);
            $data['venta_fin_mes'] = (int) ($data['venta_fin_mes'] ?? 12);
        } elseif (($data['tipo_vigencia'] ?? 'meses') === 'indefinida') {
            $data['duracion_meses'] = 1;
            $data['venta_inicio_mes'] = null;
            $data['venta_fin_mes'] = null;
        } else {
            $data['duracion_meses'] = (int) ($data['duracion_meses'] ?? 1);
            $data['venta_inicio_mes'] = null;
            $data['venta_fin_mes'] = null;
        }

        return $data;
    }
}