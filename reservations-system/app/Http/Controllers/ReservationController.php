<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationPerson;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    //
    public function index()
    {
        $restaurants = Restaurant::all();
        // Get only the newest reservations, later than yesterday's midnight
        $yesterday = Carbon::yesterday();
        $reservations = Reservation::where('start_date', '>=', $yesterday)->get();
        return view('reservations', ['restaurants' => $restaurants, 'reservations' => $reservations]);
    }

    // Refresh the page to add additional fields depending on the number of guests
    public function addFields(Request $request)
    {
        $guests = $request->input('guests');
        return redirect('reservations')->withInput()->with('guests_fields', $guests - 1);
    }

    public function create(Request $request)
    {
        $restaurant_id = $request->input('restaurant_name');
        $start_date = $request->input('date');
        $duration = $request->input('duration');
        $guests = $request->input('guests');
        $end_date = Carbon::parse($start_date)->addHours($duration);

        // Start calculating request reservation people from 1 because if there are no additional guests, we still have a reserver
        $people_count = 1;
        // Check how many guests were filled in a form (in case not all fields were filled)
        for ($i = 1; $i < $guests; $i++) {
            if (
                $request->filled('person' . $i . '_name') &&
                $request->filled('person' . $i . '_surname') &&
                $request->filled('person' . $i . '_email')
            ) {
                $people_count++;
            }
        }

        // Get certain restaurant limits (tables and max_people)
        $restaurant = Restaurant::find($restaurant_id);
        $restaurant_tables_count = $restaurant->tables;
        $restaurant_max_people = $restaurant->max_people;

        // If restaurant can't fit this amount of people at all throw an error
        if ($people_count > $restaurant_max_people) {
            return redirect('reservations')->with('error', 'Reservation cannot be made. Selected restaurant is too small for the submitted amount of people.');
        }

        // Find overlapping reservations
        $overlapping_reservations = Reservation::where('restaurant_id', $restaurant_id)->where(function ($query) use ($start_date) {
            $query->where('start_date', '<', $start_date)->where('end_date', '>', $start_date);
        })->orWhere(function ($query) use ($start_date, $end_date) {
            $query->where('start_date', '>', $start_date)->where('start_date', '<', $end_date);
        })->orWhere('start_date', $start_date)->get();

        // If overlapping reservations count not 0, proceed
        // If it's zero, no other reservations exist, not worth checking if there is any free space
        if ($overlapping_reservations->count() != 0) {
            // Calculate how many people will be at restaurant during that time
            $reservations_people = 0;
            foreach ($overlapping_reservations as $reservation) {
                $reservations_people += $reservation->people_count;
            }
            // If request reservation people added to already reserved people won't fit into the restaurant throw an error
            if (($people_count + $reservations_people) > $restaurant_max_people) {
                return redirect('reservations')->with('error', 'Reservation cannot be made. Not enough space left in this restaurant for submitted amount of people.');
            }

            // Calculate how many tables are used during that time
            $reservations_tables = 0;
            foreach ($overlapping_reservations as $reservation) {
                $reservations_tables += $reservation->tables_count;
            }
            // If all tables reserved (or more if there was a bug and system allowed that), return an error and not allow to make a reservation
            if ($reservations_tables >= $restaurant_tables_count) {
                return redirect('reservations')->with('error', 'Reservation cannot be made. No free tables left in this restaurant.');
            }

            // Get this restaurant's tables and seats counts, and sort asc by seats count
            $tables_seats = RestaurantTable::where('restaurant_id', $restaurant_id)->get(['people_count']);
            $tables_seats = $tables_seats->sortBy('people_count');
            // Make a copy of seats counts to array
            $seats_array = array();
            foreach ($tables_seats as $tables_seat) {
                array_push($seats_array, $tables_seat->people_count);
            }

            // Check what tables each existing reservation uses and remove these tables from $seats_array, so only free tables remain
            foreach ($overlapping_reservations as $reservation) {
                $overlapping_people_count = $reservation->people_count; // Existing reservation people count
                while ($overlapping_people_count > 0) { // While there are people without table
                    $found = false; // Was table found?
                    foreach ($seats_array as $key => $value) { // Going through tables to find the needed one
                        if ($value >= $overlapping_people_count) { // Searching for a table that has equal or more seats
                            $overlapping_people_count -= $seats_array[$key]; // Remove table seats count from unseated people count
                            unset($seats_array[$key]); // Remove first found table from the list that has enough seats
                            $found = true; // Table was found
                            break;
                        }
                    }
                    if (!$found) { // If good table wasn't found
                        $overlapping_people_count -= array_pop($seats_array); // Take the biggest table from the end of array
                    }
                }
            }

            // Check if there are enough tables for request reservation people
            $request_people_count = $people_count;
            $tables_count = 0; // Calculate how many tables it will need
            while ($request_people_count > 0) { // While there are people without table
                if (empty($seats_array)) { // If no tables left anymore
                    return redirect('reservations')->with('error', 'Reservation cannot be made. No free tables left in this restaurant.');
                }
                $found = false; // Was table found?
                foreach ($seats_array as $key => $value) { // Going through tables to find the needed one
                    if ($value >= $request_people_count) { // Searching for a table that has equal or more seats
                        $request_people_count -= $seats_array[$key]; // Remove table seats count from unseated people count
                        unset($seats_array[$key]); // Remove first found table from the list that has enough seats
                        $found = true; // Table was found
                        $tables_count++;
                        break;
                    }
                }
                if (!$found) { // If good table wasn't found
                    $request_people_count -= array_pop($seats_array); // Take the biggest table from the end of array
                    $tables_count++;
                }
            }
        } else { // If no overlapping reservations found, just check how many tables request reservation needs
            // Get this restaurant's tables and seats counts, and sort asc by seats count
            $tables_seats = RestaurantTable::where('restaurant_id', $restaurant_id)->get(['people_count']);
            $tables_seats = $tables_seats->sortBy('people_count');
            // Make a copy of seats counts to array
            $seats_array = array();
            foreach ($tables_seats as $tables_seat) {
                array_push($seats_array, $tables_seat->people_count);
            }
            // Check if there are enough tables for request reservation people
            $request_people_count = $people_count;
            $tables_count = 0; // Calculate how many tables it will need
            while ($request_people_count > 0) { // While there are people without table
                if (empty($seats_array)) { // If no tables left anymore
                    return redirect('reservations')->with('error', 'Reservation cannot be made. No free tables left in this restaurant.');
                }
                $found = false; // Was table found?
                foreach ($seats_array as $key => $value) { // Going through tables to find the needed one
                    if ($value >= $request_people_count) { // Searching for a table that has equal or more seats
                        $request_people_count -= $seats_array[$key]; // Remove table seats count from unseated people count
                        unset($seats_array[$key]); // Remove first found table from the list that has enough seats
                        $found = true; // Table was found
                        $tables_count++;
                        break;
                    }
                }
                if (!$found) { // If good table wasn't found
                    $request_people_count -= array_pop($seats_array); // Take the biggest table from the end of array
                    $tables_count++;
                }
            }
        }

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
            'tables_count' => $tables_count,
            'people_count' => $people_count
        ]);
        $created_reservation_id = $created_reservation->id;

        // Save additional guests list
        for ($i = 1; $i < $people_count; $i++) {
            if (
                $request->filled('person' . $i . '_name') &&
                $request->filled('person' . $i . '_surname') &&
                $request->filled('person' . $i . '_email')
            ) {
                ReservationPerson::create([
                    'reservations_id' => $created_reservation_id,
                    'add_name' => $request->input('person' . $i . '_name'),
                    'add_surname' => $request->input('person' . $i . '_surname'),
                    'add_email' => $request->input('person' . $i . '_email'),
                ]);
            }
        }

        return redirect('reservations')->with('success', 'true');
    }
}
