@extends('layout')

@section('content')
<center>
    <h2>Make a reservation</h2>
</center>
@if(Session::has('success'))
<div class="container">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Reservation was created successfully!</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif
<!-- <div class="container">
    <form>
        <div class="row">
            <div class="form-group col-md-3">
                <label for="people">Number of guests (including you)</label>
                <input type="number" class="form-control" id="people" name="people" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</butto </form>
</div> -->
<div class="container">
    <form action="{{ route('add_reservation') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="restaurant_name">Restaurant name *</label>
            <select class="form-select" id="restaurant_name" name="restaurant_name" required>
                <option value="">Choose a restaurant</option>
                @if(isset($restaurants))
                @foreach($restaurants as $restaurant)
                <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="date">Reservation date *</label>
                <input type="datetime-local" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group col">
                <label for="duration">Duration *</label>
                <select class="form-select" id="duration" name="duration">
                    <option value="1">1 hour</option>
                    <option value="2">2 hours</option>
                    <option value="3">3 hours</option>
                    <option value="4">4 hours</option>
                    <option value="5">5 hours</option>
                    <option value="6">6 hours</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="reserver_name">Reserver Name *</label>
                <input type="text" class="form-control" id="reserver_name" name="reserver_name" placeholder="John" required>
            </div>
            <div class="form-group col">
                <label for="reserver_surname">Reserver Surname *</label>
                <input type="text" class="form-control" id="reserver_surname" name="reserver_surname" placeholder="Smith" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="reserver_email">Reserver Email *</label>
                <input type="email" class="form-control" id="reserver_email" name="reserver_email" placeholder="name@example.com" required>
            </div>
            <div class="form-group col">
                <label for="reserver_phone">Reserver Phone *</label>
                <input type="text" class="form-control" id="reserver_phone" name="reserver_phone" placeholder="+37061234567" required>
            </div>
        </div><br>
        <center><p><b>Additional guests list</b></p></center>
        @for ($i = 1; $i <= 20; $i++)
        <div class="row">
            <div class="form-group col">
                <label for="person{{$i}}_name">Name</label>
                <input type="text" class="form-control" id="person{{$i}}_name" name="person{{$i}}_name" placeholder="Name {{$i}}">
            </div>
            <div class="form-group col">
                <label for="person{{$i}}_surname">Surname</label>
                <input type="text" class="form-control" id="person{{$i}}_surname" name="person{{$i}}_surname" placeholder="Surname {{$i}}">
            </div>
            <div class="form-group col">
                <label for="person{{$i}}_email">Email</label>
                <input type="email" class="form-control" id="person{{$i}}_email" name="person{{$i}}_email" placeholder="name{{$i}}@example.com">
            </div>
        </div>
        @endfor
        <br>
        <button type="submit" class="btn btn-primary">Submit</button> * - required fields
    </form>
</div>

<div class="container">
    <table class="table table-hover">
        <tr class="table-success">
            <th>#</th>
            <th>Restaurant</th>
            <th>Reserver Name</th>
            <th>Reserver Surname</th>
            <th>Reserver Email</th>
            <th>Reserver Phone</th>
            <th>Reservation Date</th>
            <th>Duration</th>
        </tr>
        @if(isset($reservations))
        @foreach($reservations as $reservation)
        <tr>
            <td>{{ $reservation->id }}</td>
            <td>{{ $reservation->restaurant->name }}</td>
            <td>{{ $reservation->reserver_name }}</td>
            <td>{{ $reservation->reserver_surname }}</td>
            <td>{{ $reservation->reserver_email }}</td>
            <td>{{ $reservation->reserver_phone }}</td>
            <td>{{ $reservation->start_date }}</td>
            <td>{{ $reservation->duration }} hour(s)</td>
        </tr>
        @endforeach
        @endif
    </table>
</div>
@stop