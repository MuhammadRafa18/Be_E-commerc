<?php

use App\Http\Controllers\Banner;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DataUser;
use App\Http\Controllers\ParagrafAbout;
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
Route::apiResource('UserAdmin',UserAdmin::class);
Route::apiResource('DataUser',DataUser::class);
Route::apiResource('banner',Banner::class);
Route::apiResource('ProdukType',ProdukType::class);
Route::apiResource('result',ControllersResult::class);
Route::apiResource('visimisi',VisiMisi::class);
Route::apiResource('paragrafabout',ParagrafAbout::class);
