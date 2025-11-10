<?php

use App\Http\Controllers\AddresController;
use App\Http\Controllers\AuthDataUserController;
use App\Http\Controllers\AuthUserAdmin;
use App\Http\Controllers\Banner;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DataUser;
use App\Http\Controllers\DetailFaq;
use App\Http\Controllers\Faq;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Order;
use App\Http\Controllers\ParagrafAbout;
use App\Http\Controllers\Power;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProdukType;
use App\Http\Controllers\Result as ControllersResult;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserAdmin;
use App\Http\Controllers\VisiMisi;
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
Route::apiResource('category',CategoryController::class);
Route::apiResource('type',TypeController::class);
Route::apiResource('produk',ProdukController::class);
Route::apiResource('DataUser',DataUser::class);
Route::apiResource('banner',Banner::class);
Route::apiResource('ProdukType',ProdukType::class);
Route::apiResource('result',ControllersResult::class);
Route::apiResource('visimisi',VisiMisi::class);
Route::apiResource('paragrafabout',ParagrafAbout::class);
Route::apiResource('power',Power::class);
Route::apiResource('faq',Faq::class);
Route::apiResource('detailfaq',DetailFaq::class);
Route::apiResource('addres',AddresController::class);
Route::apiResource('order',Order::class);

Route::post('login', [AuthDataUserController::class, 'login']);
Route::post('logout', [AuthDataUserController::class, 'login']);
Route::post('favorite/toggle', [FavoriteController::class, 'toggleOn']);
Route::get('favorite', [FavoriteController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logoutAdmin', [AuthUserAdmin::class, 'logout']);

    
});
Route::post('loginAdmin', [AuthUserAdmin::class, 'login']);

Route::middleware(['auth:sanctum', 'role:SuperAdmin'])->group(function () {
Route::apiResource('UserAdmin',UserAdmin::class);
     
});
