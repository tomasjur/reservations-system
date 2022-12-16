<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    // Sets the table to use for restaurants.
    protected $table = 'restaurants';

    // Protected setter for fillable.
    protected $fillable = [
        'name', 'tables', 'max_people'
    ];

    // One To Many, one restaurant can have many tables
    public function restaurant_tables() {
        return $this->hasMany(RestaurantTable::class, 'restaurant_id');
    }

    // One To Many, one restaurant can have many reservations
    public function reservations() {
        return $this->hasMany(Reservation::class, 'restaurant_id');
    }
}
