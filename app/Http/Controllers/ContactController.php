<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactoMail;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();

        // Guardamos en BD (va bien para no perder mensajes)
        $msg = ContactMessage::create([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'asunto'   => $data['asunto'],
            'mensaje'  => $data['mensaje'],
        ]);

        // Mandamos mail al club (si falla, no rompemos el flujo)
        try {
            Mail::to(config('mail.contact_to'))->send(new ContactoMail($msg));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('ok', '¡Mensaje enviado! Te contestaremos lo antes posible.');
    }
}