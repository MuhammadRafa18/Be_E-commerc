<?php

namespace App\Mail;

use App\Models\ProductSku;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

 
    public ProductSku $sku;

    /**
     * Create a new message instance.
     */
    public function __construct(ProductSku $sku)
    {
        $this->sku = $sku;
    }

    /**
     * Get the message envelope (Judul Email).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "PERINGATAN: Stok Produk {$this->sku->product->title} Hampir Habis!",
        );
    }

    /**
     * Get the message content definition (Tampilan HTML Email).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.low_stock_alert', 
        );
    }
}