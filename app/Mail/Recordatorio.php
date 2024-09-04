<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Recordatorio extends Mailable
{
    use Queueable, SerializesModels;
    public $detalle;
    public $name;
    /**
     * Create a new message instance.
     */
    public function __construct($detalle,$name)
    {   
        $this->detalle = $detalle;
        $this->name = $name;
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vacante nuevo de '.$this->detalle,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'plantilla.correo',
            with: [
                'detalle' => $this->detalle, 
                'nombre' => $this->name
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
