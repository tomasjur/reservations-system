<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationPerson extends Model
{
    use HasFactory;

    // Sets the reservation_people table.
    protected $table = 'reservations_people';

    // Protected setter for fillable.
    protected $fillable = [
        'reservations_id', 'add_name', 'add_surname', 'add_email'
    ];

    // One To Many Inverse, to find which reservation belongs to the person
    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }
}
