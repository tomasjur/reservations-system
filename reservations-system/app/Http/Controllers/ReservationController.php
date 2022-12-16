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
    
    // Refresh the page to add additional fields depending on the number of guests
    public function addFields(Request $request) {
        $guests = $request->input('guests');
        return redirect('reservations')->withInput()->with('guests_fields', $guests-1);
    }

    public function create(Request $request) {
        $restaurant_id = $request->input('restaurant_name');
        $start_date = $request->input('date');
        $duration = $request->input('duration');
        $guests = $request->input('guests');
        $end_date = Carbon::parse($start_date)->addHours($duration);

        // Start calculating request reservation people from 1 because if there are no additional guests, we still have a reserver
        $people_count = 1;
        // Check how many guests were filled in a form (in case not all fields were filled)
        for ($i = 1; $i < $guests; $i++) {
            if ($request->filled('person'.$i.'_name') && 
                $request->filled('person'.$i.'_surname') &&
                $request->filled('person'.$i.'_email')) {
                $people_count++;
            }
        }

        // Get certain restaurant limits (tables and max_people)
        $restaurant = Restaurant::find($restaurant_id);
        $restaurant_tables = $restaurant->tables;
        $restaurant_max_people = $restaurant->max_people;

        // If restaurant can't fit this amount of people at all throw an error
        if ($people_count > $restaurant_max_people) {
            return redirect('reservations')->with('error', 'Reservation cannot be made. Selected restaurant do not accept this amount of people.');
        }
        
        // Find all reservations later than yesterday's midnight (to not get old reservations)
        $yesterday = Carbon::yesterday();
        $restaurant_reservations = Reservation::where('restaurant_id', $restaurant_id)->where('start_date', '>=', $yesterday)->get();
        // Find overlapping reservations
        $overlapping_reservations = $restaurant_reservations->where([
                                        ['start_date', '<', $start_date],
                                        ['end_date', '>', $start_date],
                                    ])->orWhere([
                                        ['start_date', '>', $start_date],
                                        ['start_date', '<', $end_date],
                                    ])->orWhere('start_date', $start_date)->get();
        
        // Calculate how many people will be at restaurant during that time
        
        // Calculate how many tables are used during requested time


        // Remaining form inputs to create a reservation
        $reserver_name = $request->input('reserver_name');
        $reserver_surname = $request->input('reserver_surname');
        $reserver_email = $request->input('reserver_email');
        $reserver_phone = $request->input('reserver_phone');

        $created_reservation = Reservation::create([
            'restaurant_id' => $restaurant_id,
            'reserver_name' => $reserver_name,
            'reserver_surname' => $reserver_surname,
            'reserver_email' => $reserver_email,
            'reserver_phone' => $reserver_phone,
            'start_date' => $start_date,
            'duration' => $duration,
            'end_date' => $end_date,
            'people_count' => $guests
        ]);
        $created_reservation_id = $created_reservation->id;

        // Save additional guests list

        return redirect('reservations')->with('success', 'true');
    }
}
