<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Sets the table to use for reservations.
    protected $table = 'reservations';

    // Protected setter for fillable.
    protected $fillable = [
        'restaurant_id', 'reserver_name', 'reserver_surname', 'reserver_email', 'reserver_phone', 'start_date', 'duration', 'end_date'
    ];

    // One To Many Inverse, to find which restaurant belongs to the reservation
    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }

    // One To Many, one reservation can have many people
    public function reservation_people() {
        return $this->hasMany(ReservationPerson::class, 'reservations_id');
    }
}
