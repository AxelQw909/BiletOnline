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

    // Передаем коллекцию объектов билетов
    public $tickets; 
    public $customerName;
    public $concert;

    /**
     * Create a new message instance.
     */
    public function __construct($tickets, $customerName, $concert)
    {
        $this->tickets = $tickets; // Здесь ожидается результат запроса Ticket::where(...)->get()
        $this->customerName = $customerName;
        $this->concert = $concert;
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