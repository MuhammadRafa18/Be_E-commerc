<?php

namespace App\Console\Commands;

use App\Mail\LowStockAlertMail;
use App\Models\ProductSku;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckLowStockProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:check-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek produk yang stoknya menipis dan mengirimkan alert';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batasMin = 5;
        $lowStockSkus = ProductSku::with('product')->where('is_active', true)
            ->where('stock', '<=', $batasMin)
            ->where('stock', '>', 0)->get();

        if ($lowStockSkus->isEmpty()) {
            $this->info('Aman! Semua stok produk masih mencukupi.');
            return self::SUCCESS;
        }
        $this->warn("Ditemukan {$lowStockSkus->count()} produk yang hampir habis!");
        foreach ($lowStockSkus as $sku) {
            $pesan = "PERINGATAN: Stok {$sku->product->title} (SKU: {$sku->name}) sisa {$sku->stock} pcs! Segera isi ulang.";

            $this->line($pesan);
            Log::warning($pesan);

            Mail::to('admin@tokomu.com')->send(new LowStockAlertMail($sku));
        }

        return self::SUCCESS;
    }
}
