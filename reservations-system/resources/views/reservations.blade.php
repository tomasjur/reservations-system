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
@if(Session::has('error'))
<div class="container">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ Session::get('error') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif
<div class="container">
    @if(Session::has('guests_fields'))
    <form action="{{ route('add_reservation') }}" method="POST">
    @else
    <form action="{{ route('addFields_reservation') }}" method="POST">
    @endif
        @csrf
        <div class="form-group">
            <label for="restaurant_name">Restaurant name *</label>
            <select class="form-select" id="restaurant_name" name="restaurant_name" required>
                <option value="">Choose a restaurant</option>
                @if(isset($restaurants))
                @foreach($restaurants as $restaurant)
                @if(old('restaurant_name') == $restaurant->id)
                <option value="{{ $restaurant->id }}" selected>{{ $restaurant->name }}</option>
                @else
                <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                @endif
                @endforeach
                @endif
            </select>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="date">Reservation date *</label>
                <input type="datetime-local" class="form-control" id="date" name="date" value="{{ old('date') }}" required>
            </div>
            <div class="form-group col">
                <label for="duration">Duration *</label>
                <select class="form-select" id="duration" name="duration">
                    @for ($i = 1; $i <= 6; $i++)
                    @if(old('duration') == $i)
                    <option value="{{$i}}" selected>{{$i}} hour(s)</option>
                    @else
                    <option value="{{$i}}">{{$i}} hour(s)</option>
                    @endif
                    @endfor
                </select>
            </div>
            <div class="form-group col">
                <label for="guests">Number of guests (including you) *</label>
                @if(Session::has('guests_fields'))
                <input type="number" class="form-control" id="guests" name="guests" min="1" value="{{ old('guests') }}" readonly>
                @else
                <input type="number" class="form-control" id="guests" name="guests" min="1" required>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="reserver_name">Reserver Name *</label>
                <input type="text" class="form-control" id="reserver_name" name="reserver_name" placeholder="John" value="{{ old('reserver_name') }}" required>
            </div>
            <div class="form-group col">
                <label for="reserver_surname">Reserver Surname *</label>
                <input type="text" class="form-control" id="reserver_surname" name="reserver_surname" placeholder="Smith" value="{{ old('reserver_surname') }}" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="reserver_email">Reserver Email *</label>
                <input type="email" class="form-control" id="reserver_email" name="reserver_email" placeholder="name@example.com" value="{{ old('reserver_email') }}" required>
            </div>
            <div class="form-group col">
                <label for="reserver_phone">Reserver Phone *</label>
                <input type="text" class="form-control" id="reserver_phone" name="reserver_phone" placeholder="+37061234567" value="{{ old('reserver_phone') }}" required>
            </div>
        </div><br>
        @if(Session::has('guests_fields'))
        @php
        $guests_fields = Session::get('guests_fields')
        @endphp
        <center><p><b>Additional guests list</b></p></center>
        @for ($i = 1; $i <= $guests_fields; $i++)
        <div class="row">
            <div class="form-group col">
                <label for="person{{$i}}_name">Name</label>
                <input type="text" class="form-control" id="person{{$i}}_name" name="person{{$i}}_name" placeholder="Name {{$i}}" value="MyName">
            </div>
            <div class="form-group col">
                <label for="person{{$i}}_surname">Surname</label>
                <input type="text" class="form-control" id="person{{$i}}_surname" name="person{{$i}}_surname" placeholder="Surname {{$i}}" value="MySurname">
            </div>
            <div class="form-group col">
                <label for="person{{$i}}_email">Email</label>
                <input type="email" class="form-control" id="person{{$i}}_email" name="person{{$i}}_email" placeholder="name{{$i}}@example.com" value="name@example.com">
            </div>
        </div>
        @endfor
        <br>
        @endif
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
            <th>People</th>
            <th>Tables</th>
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
            <td>{{ $reservation->people_count }}</td>
            <td>{{ $reservation->tables_count }}</td>
        </tr>
        @endforeach
        @endif
    </table>
</div>
@stop