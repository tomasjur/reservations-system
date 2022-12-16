<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationPerson;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    //
    public function index() {
        $restaurants = Restaurant::all();
        $reservations = Reservation::all();
        return view('reservations', ['restaurants' => $restaurants, 'reservations' => $reservations]);
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

        // Start calculating request reservation people from 1
        // because if there are no additional guests, we still have a reserver
        $people_count = 1;
        // Check how many guests were filled in a form
        for ($i = 1; $i <= 20; $i++) {
            if ($request->filled('person'.$i.'_name') && 
                $request->filled('person'.$i.'_surname') &&
                $request->filled('person'.$i.'_email')) {
                $people_count++;
            }
        }
        // Considering that each table can have 4 people, we calculate how many tables we will need
        $tables = ceil($people_count / 4);

        // Get reservation ids from DB
        $reservations_ids = Reservation::where('restaurant_id', $restaurant_id)->get('id');
        // Count how many reservations there exists (1 reserver for each reservation)
        $already_reserved_people = count($reservations_ids);
        // Find the total amount of additional guests among all reservations
        foreach ($reservations_ids as $reservations_id) {
            $already_reserved_people += ReservationPerson::where('reservations_id', $reservations_id)->count();
        }
        // Considering that each table can have 4 people, we calculate how many tables are already reserved
        $already_reserved_tables = ceil($already_reserved_people / 4);

        // Get certain restaurant limits (tables and max_people)
        $restaurant = Restaurant::where('id', $restaurant_id)->first();
        $restaurant_tables = $restaurant->tables;
        $restaurant_max_people = $restaurant->max_people;


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
