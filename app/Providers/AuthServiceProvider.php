<?php

namespace App\Providers;

use App\Models\Addres;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Policies\AddressPolicy;
use App\Policies\CartPolicy;
use App\Policies\FavoritePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Order::class => OrderPolicy::class,
        Product::class => ProductPolicy::class,
        Payment::class => PaymentPolicy::class,
        Cart::class => CartPolicy::class,
        Addres::class => AddressPolicy::class,
        Favorite::class => FavoritePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
         VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifikasi Email')
                ->greeting('Halo!')
                ->line('Terima kasih telah mendaftar.')
                ->line('Silakan klik tombol di bawah untuk memverifikasi email Anda.')
                ->action('Verifikasi Email', $url)
                ->line('Jika Anda tidak merasa mendaftar, abaikan email ini.');
        });
    }
}
