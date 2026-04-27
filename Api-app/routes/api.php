<?php

use App\Http\Controllers\Api\Admin\About;
use App\Http\Controllers\Api\Admin\Banner as AdminBanner;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\User\DataUser as AdminDataUser;
use App\Http\Controllers\Api\Admin\Faq_category;
use App\Http\Controllers\Api\User\FavoriteController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\SkinTypes;
use App\Http\Controllers\Api\Admin\Result as AdminResult;
use App\Http\Controllers\Api\Admin\ShippingZone;
use App\Http\Controllers\Api\Admin\UserAdmin;
use App\Http\Controllers\Api\Admin\ZoneRegion;
use App\Http\Controllers\Api\Auth\AuthDataUserController;
use App\Http\Controllers\Api\Auth\AuthUserAdmin;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\User\AddresController;
use App\Http\Controllers\Api\User\DetailFaq;
use App\Http\Controllers\Api\User\Order;
use App\Http\Controllers\Api\Auth\ResendVerificationController;
use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Controllers\Api\User\PhoneVertivication;
use App\Http\Controllers\Api\User\Cart;
use App\Http\Controllers\Api\User\ContactController;
use App\Http\Controllers\Api\User\PaymentController;
use App\Http\Controllers\Api\User\VisitorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//  Hak Ases Guest
// Auth
Route::prefix('auth')->group(function () {
    Route::post('loginAdmin', [AuthUserAdmin::class, 'login']);
    Route::post('login', [AuthDataUserController::class, 'login']);
    //  login with google
    Route::get('/google', [GoogleAuthController::class, 'redirect']);
    Route::get('/google/callback', [GoogleAuthController::class, 'callback']);
});
// verif email
Route::get(
    '/email/verify/{id}/{hash}',
    [VerificationController::class, 'verify']
)->middleware(['signed'])->name('verification.verify');

//  resen verif email
Route::post('/email/resend', [ResendVerificationController::class, 'resend'])->middleware(['throttle:5,1']);
// verif nomor 
Route::get('/phone/verify/{token}', [PhoneVertivication::class, 'verify']);

// webhook midtrans
    Route::post('/midtrans/callback', [PaymentController::class, 'callback']);

// Register
Route::post('register', [AdminDataUser::class, 'register']);
// Produk
Route::get('product', [ProductController::class, 'index']);
Route::post('product/{slug}', [ProductController::class, 'show']);
// Faq Category
Route::get('Faq_category', [Faq_category::class, 'index']);
// Visitor
Route::post('/visitor', [VisitorController::class, 'store']);
// CATEGORY
Route::get('category', [AdminCategoryController::class, 'index']);

// SKIN TYPE
Route::get('SkinTypes', [SkinTypes::class, 'index']);
Route::post('SkinTypes/{slug}', [SkinTypes::class, 'show']);
// Banner
Route::get('banner', [AdminBanner::class, 'index']);
Route::get('banner/{Banner}', [AdminBanner::class, 'show']);
//About
Route::post('about/{slug}', [About::class, 'show']);
// Detail Faq
Route::get('DetailFaq', [DetailFaq::class, 'index']);
Route::post('DetailFaq/{slug}', [DetailFaq::class, 'show']);
// Contact
Route::post('Contact',[ContactController::class,'store']);
// Result
Route::get('result', [AdminResult::class, 'index']);
//  Logout
Route::middleware('auth:sanctum')->group(function () {
    //  Logout  User
    Route::post('logout', [AuthDataUserController::class, 'logout'])->middleware('auth:sanctum');
    // Logout Admin
    Route::post('logoutAdmin', [AuthUserAdmin::class, 'logout'])->middleware('auth:sanctum');
});


//  Hak Ases User
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    //  Addres
    Route::apiResource('addres', AddresController::class);

    // Order
    Route::get('order', [Order::class, 'user']);
    Route::post('order', [Order::class, 'checkout']);
    Route::post('order/{id}', [Order::class, 'show']);
    Route::patch('order/cancel/{order}', [Order::class, 'destroy']);


    //  User
    Route::get('user/profile', [AdminDataUser::class, 'show']);
    Route::put('user/me', [AdminDataUser::class, 'update']);
    Route::patch('user/me', [AdminDataUser::class, 'update']);
    Route::delete('user/delete', [AdminDataUser::class, 'destroy']);

    // Favorite
    Route::get('favorite', [FavoriteController::class, 'index']);
    Route::post('favorite', [FavoriteController::class, 'toggleOn']);

    // Cart
    Route::get('cart', [Cart::class, 'index']);
    Route::post('cart', [Cart::class, 'store']);
    Route::delete('cart/delete/{id}', [Cart::class, 'destroy']);
    Route::post('cart/selected/{id}', [Cart::class, 'select'])->middleware('throttle:20,1');
    

    // Verif Phone
    Route::post('/phone/request', [PhoneVertivication::class, 'phone'])->middleware(['throttle:2,1']);

    // Payment
    Route::post('payment/{id}', [PaymentController::class, 'create']);
});


// Hak ases Admin dn Super admin
Route::middleware(['auth:sanctum', 'role:admin|super_admin'])
    ->prefix('admin')
    ->group(function () {   

        // Category
        Route::get('category/{category}', [AdminCategoryController::class, 'show']);
        Route::post('category', [AdminCategoryController::class, 'store']);
        Route::put('category/{category}', [AdminCategoryController::class, 'update']);
        Route::patch('category/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('category/{category}', [AdminCategoryController::class, 'destroy']);

        // Order
        Route::get('order', [Order::class, 'index']);
        Route::put('order/{order}', [Order::class, 'update']);
        Route::patch('order/{order}', [Order::class, 'update']);


        // Produk
        Route::post('product', [ProductController::class, 'store']);
        Route::put('product/{product}', [ProductController::class, 'update']);
        Route::patch('product/{product}', [ProductController::class, 'update']);
        Route::delete('product/{product}', [ProductController::class, 'destroy']);

        //Banner
        Route::post('banner', [AdminBanner::class, 'store']);
        Route::put('banner/{Banner}', [AdminBanner::class, 'update']);
        Route::patch('banner/{Banner}', [AdminBanner::class, 'update']);
        Route::delete('banner/{Banner}', [AdminBanner::class, 'destroy']);
        // Produk Skin Type
        Route::post('SkinTypes', [SkinTypes::class, 'store']);
        Route::put('SkinTypes/{Skin_type}', [SkinTypes::class, 'update']);
        Route::patch('SkinTypes/{Skin_type}', [SkinTypes::class, 'update']);
        Route::delete('SkinTypes/{Skin_type}', [SkinTypes::class, 'destroy']);

        // Result
        Route::get('result/{result}', [AdminResult::class, 'show']);
        Route::post('result', [AdminResult::class, 'store']);
        Route::put('result/{result}', [AdminResult::class, 'update']);
        Route::delete('result/{result}', [AdminResult::class, 'destroy']);

        // About
        Route::get('about', [About::class, 'index']);
        Route::post('about', [About::class, 'store']);
        Route::put('about/{about}', [About::class, 'update']);
        Route::patch('about/{about}', [About::class, 'update']);
        Route::delete('about/{about}', [About::class, 'destroy']);

        // Faq Category
        Route::get('Faq_category/{Faq_category}', [Faq_category::class, 'show']);
        Route::post('Faq_category', [Faq_category::class, 'store']);
        Route::put('Faq_category/{Faq_category}', [Faq_category::class, 'update']);
        Route::patch('Faq_category/{Faq_category}', [Faq_category::class, 'update']);
        Route::delete('Faq_category/{Faq_category}', [Faq_category::class, 'destroy']);

        // Detail Faq
        Route::post('DetailFaq', [DetailFaq::class, 'store']);
        Route::put('DetailFaq/{detail_faq}', [DetailFaq::class, 'update']);
        Route::patch('DetailFaq/{detail_faq}', [DetailFaq::class, 'update']);
        Route::delete('DetailFaq/{detail_faq}', [DetailFaq::class, 'destroy']);

        // Contact
        Route::get('Contact', [ContactController::class, 'index']);
        Route::get('Contact/{contact}', [ContactController::class, 'show']);
        Route::delete('Contact/{contact}', [ContactController::class, 'destroy']);

        // User 
         Route::get('me', [UserAdmin::class, 'me']);

        // User Client
        Route::get('DataUser', [AdminDataUser::class, 'index']);

        //  Shipping Zone
        Route::apiResource('shippingZone',ShippingZone::class);
        // Zone Region
        Route::apiResource('zoneRegion',ZoneRegion::class);
        // visitor
        Route::get('/visitor', [VisitorController::class, 'index']);
    });



//  Hak Ases Super Admin  
Route::middleware(['auth:sanctum', 'role:super_admin'])
    ->prefix('admin')
    ->group(function () {
        Route::apiResource('UserAdmin', UserAdmin::class);
    });
