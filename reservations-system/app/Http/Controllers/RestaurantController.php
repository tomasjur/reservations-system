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
        return view('restaurants');
    }

    // Refresh the page in order to add additional fields
    public function addFields(Request $request) {
        $tables = $request->input('tables');
        return redirect('restaurants')->withInput()->with('tables_fields', $tables);
    }

    // Create a new restaurant
    public function create(Request $request) {
        $name = $request->input('name');
        $tables = $request->input('tables');
        $max_people = $request->input('max_people');

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'tables' => 'required',
            'max_people' => 'required'
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
            'max_people' => $max_people
        ]);
        // Get created restaurant ID
        $restaurant_id = $restaurant->id;

        // Save each filled table data
        for ($i = 1; $i <= $tables; $i++) {
            if ($request->filled('table'.$i)) {
                RestaurantTable::create([
                    'restaurant_id' => $restaurant_id,
                    'people_count' => $request->input('table'.$i)
                ]);
            }
        }

        return redirect('restaurants')->with('success', 'true');
    }
}
