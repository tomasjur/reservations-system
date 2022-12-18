<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    //
    public function index() {
        $restaurants = Restaurant::all();
        $tables = RestaurantTable::all();
        return view('restaurants', ['restaurants' => $restaurants, 'tables' => $tables]);
    }

    // Refresh the page to add additional fields depending on the number of tables
    public function addFields(Request $request) {
        $tables = $request->input('tables');
        return redirect('restaurants')->withInput()->with('tables_fields', $tables);
    }

    // Create a new restaurant
    public function create(Request $request) {
        $name = $request->input('name');
        $tables = $request->input('tables');

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'tables' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('/restaurants')
                        ->withErrors($validator)
                        ->withInput();
        }

        // Create a new restaurant
        $restaurant = Restaurant::create([
            'name' => $name,
            'tables' => $tables,
        ]);
        // Get created restaurant ID
        $restaurant_id = $restaurant->id;

        // Save each filled table data
        $max_people = 0;
        for ($i = 1; $i <= $tables; $i++) {
            if ($request->filled('table'.$i)) {
                RestaurantTable::create([
                    'restaurant_id' => $restaurant_id,
                    'people_count' => $request->input('table'.$i)
                ]);
                $max_people += $request->input('table'.$i);
            }
        }

        // Update max_people count in DB
        $restaurant->max_people = $max_people;
        $restaurant->save();

        // Return with success message
        return redirect('restaurants')->with('success', 'true');
    }
}
