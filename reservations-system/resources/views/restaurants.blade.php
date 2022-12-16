@extends('layout')

@section('content')
<center>
    <h2>Add a new restaurant</h2>
</center>
@foreach ($errors->all() as $error)
<div>{{ $error }}</div>
@endforeach
@if(Session::has('success'))
<div class="container">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Restaurant was successfully added!</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif
<div class="container">
    @if(Session::has('tables_fields'))
    <form action="{{ route('add_restaurant') }}" method="POST">
    @else
    <form action="{{ route('addFields_restaurant') }}" method="POST">
    @endif
        @csrf
        <div class="form-group">
            <label for="name">Restaurant name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="e. g. Oxomoco" value="{{ old('name') }}" required>
        </div>
        <div class="row">
            <div class="form-group col">
                <label for="tables">Number of tables</label>
                @if(Session::has('tables_fields'))
                <input type="number" class="form-control" id="tables" name="tables" min="1" value="{{ old('tables') }}" readonly>
                @else
                <input type="number" class="form-control" id="tables" name="tables" min="1" required>
                @endif
                <div id="tablesHelp" class="form-text">Minimum 1.</div>
            </div>
        </div><br>
        @if(Session::has('tables_fields'))
        @php
        $tables_fields = Session::get('tables_fields')
        @endphp
        <center><p><b>Seats count for tables</b></p></center>
        <div class="row">
        @for ($i = 1; $i <= $tables_fields; $i++)
            <div class="form-group col-md-1">
                <label for="table{{$i}}">Table {{$i}}</label>
                <input type="number" class="form-control" id="table{{$i}}" name="table{{$i}}" min="1" value="1">
            </div>
        @endfor
        </div><br>
        @endif
        <button type="submit" class="btn btn-primary">Submit</button> (all fields are required)
    </form>
</div>
@stop