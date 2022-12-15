<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    //
    public function index() {
        $restaurants = Restaurant::all();
        return view('reservations', ['restaurants' => $restaurants]);
    }
    
    public function create(Request $request) {
        $restaurant_id = $request->input('restaurant_name');
        $reserver_name = $request->input('reserver_name');
        $reserver_surname = $request->input('reserver_surname');
        $reserver_email = $request->input('reserver_email');
        $reserver_phone = $request->input('reserver_phone');
        $start_date = $request->input('date');
        $duration = $request->input('duration');
        $end_date = Carbon::parse($start_date)->addHours($duration);

        Reservation::create([
            'restaurant_id' => $restaurant_id,
            'reserver_name' => $reserver_name,
            'reserver_surname' => $reserver_surname,
            'reserver_email' => $reserver_email,
            'reserver_phone' => $reserver_phone,
            'start_date' => $start_date,
            'duration' => $duration,
            'end_date' => $end_date
        ]);
        return redirect('reservations')->with('success', 'true');
    }
}
