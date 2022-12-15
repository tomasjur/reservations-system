<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    //
    public function index() {
        return view('restaurants');
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

        Restaurant::create([
            'name' => $name,
            'tables' => $tables,
            'max_people' => $max_people
        ]);
        return view('restaurants', ['success' => 'true']);
    }
}
