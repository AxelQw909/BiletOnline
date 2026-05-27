<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    // Добавляем новые публичные свойства
    public $ticketNumbers;
    public $customerName;
    public $concert; // Свойство для хранения данных о концерте

    /**
     * Create a new message instance.
     */
    public function __construct($ticketNumbers, $customerName, $concert)
    {
        $this->ticketNumbers = $ticketNumbers;
        $this->customerName = $customerName;
        $this->concert = $concert; // Присваиваем объект концерта
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ваши билеты успешно забронированы',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // Убедитесь, что файл лежит именно по этому пути: resources/views/emails/ticket.blade.php
            view: 'emails.ticket', 
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}