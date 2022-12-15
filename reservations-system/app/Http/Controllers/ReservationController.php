<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    //
    public function index() {
        $restaurants = Restaurant::all();
        return view('reservations', ['restaurants' => $restaurants]);
    }
    
    public function create(Request $request) {
        
    }
}
