<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants_page');
Route::post('/restaurants/add', [RestaurantController::class, 'create'])->name('add_restaurant');
Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations');
Route::post('/reservations/add', [ReservationController::class, 'create'])->name('add_reservation');