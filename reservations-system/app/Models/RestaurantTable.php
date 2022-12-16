<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $table = 'restaurants_table';

    protected $fillable = [
        'restaurant_id', 'people_count'
    ];

    // One To Many Inverse, to find which restaurant belongs to the table
    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }
}
