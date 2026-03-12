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
        $data = $request->validated();
        $data['activo'] = (bool) ($data['activo'] ?? false);

        TipoCuota::create($data);

        return back()->with('ok', 'Tipo de cuota creado.');
    }

    public function update(UpdateTipoCuotaRequest $request, TipoCuota $tipoCuota)
    {
        $data = $request->validated();
        $data['activo'] = (bool) ($data['activo'] ?? false);

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
}