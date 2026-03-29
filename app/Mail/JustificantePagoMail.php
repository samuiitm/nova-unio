<?php

namespace App\Mail;

use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class JustificantePagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Pago $pago)
    {
    }

    public function build()
    {
        $this->pago->loadMissing(['alumno', 'cuota.tipoCuota']);

        $nombreAlumno = trim(($this->pago->alumno?->nombre ?? '') . ' ' . ($this->pago->alumno?->apellidos ?? ''));
        $nombreAlumnoSlug = Str::slug($nombreAlumno ?: 'alumno');
        $fecha = $this->pago->fecha_pago?->format('Y-m-d') ?? now()->format('Y-m-d');

        $pdf = Pdf::loadView('pdf.justificante-pago', [
            'pago' => $this->pago,
        ]);

        $nombreArchivo = "justificante-pago-{$this->pago->id}-{$nombreAlumnoSlug}-{$fecha}.pdf";

        return $this
            ->subject('Justificante de pago - Nova Unió')
            ->view('emails.justificante-pago-texto')
            ->attachData(
                $pdf->output(),
                $nombreArchivo,
                ['mime' => 'application/pdf']
            );
    }
}