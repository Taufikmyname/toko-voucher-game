<?php
namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoucherSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Voucher Anda untuk Pesanan ' . $this->transaction->order_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.voucher-sent',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
