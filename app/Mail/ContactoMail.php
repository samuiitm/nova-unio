<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $msg) {}

    public function build()
    {
        return $this
            ->subject('Contacto web: ' . $this->msg->asunto)
            ->view('emails.contacto');
    }
}