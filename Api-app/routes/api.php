<?php

use App\Http\Controllers\Api\Admin\About;
use App\Http\Controllers\Api\Admin\Banner as AdminBanner;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\User\DataUser as AdminDataUser;
use App\Http\Controllers\Api\Admin\Faq_category;
use App\Http\Controllers\Api\User\FavoriteController;
use App\Http\Controllers\Api\Admin\ProdukController;
use App\Http\Controllers\Api\Admin\SkinTypes;
use App\Http\Controllers\Api\Admin\Result as AdminResult;
use App\Http\Controllers\Api\Admin\UserAdmin;
use App\Http\Controllers\Api\Auth\AuthDataUserController;
use App\Http\Controllers\Api\Auth\AuthUserAdmin;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\User\AddresController;
use App\Http\Controllers\Api\User\DetailFaq;
use App\Http\Controllers\Api\User\Order;
use App\Http\Controllers\Api\User\PaymentController;
use App\Http\Controllers\ResendVerificationController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LDAP\Result;

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
Route::prefix('auth')->group(function () {
    Route::post('loginAdmin', [AuthUserAdmin::class, 'login']);
    Route::post('login', [AuthDataUserController::class, 'login']);
});
Route::get(
    '/email/verify/{id}/{hash}',
    [VerificationController::class, 'verify']
)->middleware(['signed'])->name('verification.verify');
Route::post('/email/resend', [ResendVerificationController::class, 'resend'])->middleware(['throttle:5,1']);

//  Hak Ases Guest

//  login with google
Route::get('/auth/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// Register
Route::post('register', [AdminDataUser::class, 'register']);
// Produk
Route::get('produk', [ProdukController::class, 'index']);
Route::post('produk/{slug}', [ProdukController::class, 'show']);
// Faq Category
Route::get('Faq_category', [Faq_category::class, 'index']);
Route::post('Faq_category/{slug}', [Faq_category::class, 'show']);
// CATEGORY
Route::get('category', [AdminCategoryController::class, 'index']);
Route::get('category/{category}', [AdminCategoryController::class, 'show']);
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
// Result
Route::get('result', [AdminResult::class, 'index']);
// Verif Email dan Logout
Route::middleware('auth:sanctum')->group(function () {
    //  Logout  User
    Route::post('logout', [AuthDataUserController::class, 'logout'])->middleware('auth:sanctum');
    // Logout Admin
    Route::post('logoutAdmin', [AuthUserAdmin::class, 'logout'])->middleware('auth:sanctum');


    // resend email verification

});
//  Hak Ases User
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    //  Addres
    Route::apiResource('addres', AddresController::class);


    //  User
    Route::get('user/profile', [AdminDataUser::class, 'show']);
    Route::put('user/me', [AdminDataUser::class, 'update']);
    Route::patch('user/me', [AdminDataUser::class, 'update']);
    Route::delete('user/delete', [AdminDataUser::class, 'destroy']);

    // Favorite
    Route::get('favorite', [FavoriteController::class, 'index']);
    Route::post('favorite', [FavoriteController::class, 'toggleOn']);

    // 
});


// Hak ases Admin dn Super admin
Route::middleware(['auth:sanctum', 'verified', 'role:Admin,SuperAdmin'])
    ->prefix('admin')
    ->group(function () {

        // Category
        Route::post('category', [AdminCategoryController::class, 'store']);
        Route::put('category/{category}', [AdminCategoryController::class, 'update']);
        Route::patch('category{category}', [AdminCategoryController::class, 'update']);
        Route::delete('category/{category}', [AdminCategoryController::class, 'destroy']);


        // Produk
        Route::post('produk', [ProdukController::class, 'store']);
        Route::put('produk/{produk}', [ProdukController::class, 'update']);
        Route::patch('produk/{produk}', [ProdukController::class, 'update']);
        Route::delete('produk/{produk}', [ProdukController::class, 'destroy']);

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
        Route::post('Faq_category', [Faq_category::class, 'store']);
        Route::put('Faq_category/{Faq_category}', [Faq_category::class, 'update']);
        Route::patch('Faq_category/{Faq_category}', [Faq_category::class, 'update']);
        Route::delete('Faq_category/{Faq_category}', [Faq_category::class, 'destroy']);

        // Detail Faq
        Route::post('DetailFaq', [DetailFaq::class, 'store']);
        Route::put('DetailFaq/{detail_faq}', [DetailFaq::class, 'update']);
        Route::patch('DetailFaq/{detail_faq}', [DetailFaq::class, 'update']);
        Route::delete('DetailFaq/{detail_faq}', [DetailFaq::class, 'destroy']);

        // Order
        Route::put('order', [Order::class, 'update']);
        Route::get('order', [Order::class, 'index']);
        Route::patch('order', [Order::class, 'update']);

        // User 
        Route::get('user', [AdminDataUser::class, 'index']);
    });
   


 //  Hak Ases Super Admin  
Route::middleware(['auth:sanctum', 'verified', 'role:SuperAdmin'])
    ->prefix('admin')
    ->group(function () {
        Route::apiResource('UserAdmin', UserAdmin::class);
    });
