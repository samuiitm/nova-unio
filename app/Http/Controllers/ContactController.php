<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactoMail;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use App\Support\Phone;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();

        $msg = ContactMessage::create([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'telefono' => Phone::normalize($data['telefono'] ?? null),
            'asunto'   => $data['asunto'] ?? null,
            'mensaje'  => $data['mensaje'],
        ]);

        $to = config('mail.contact_to') ?: config('mail.from.address');

        $smtp = config('mail.mailers.smtp');
        $hasSmtp = !empty($smtp['host'])
            && $smtp['host'] !== '127.0.0.1'
            && !empty($smtp['username']);

        $mailer = config('mail.default');
        if (in_array($mailer, ['log', 'array'], true) && $hasSmtp) {
            $mailer = 'smtp';
        }

        try {
            Mail::mailer($mailer)->to($to)->send(new ContactoMail($msg));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('ok', 'Se ha enviado correctamente. Te responderemos lo antes posible.');
    }
}