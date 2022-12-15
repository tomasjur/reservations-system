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
    <form action="{{ route('add_restaurant') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Restaurant name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="e. g. Oxomoco" required>
        </div>
        <div class="form-group">
            <label for="tables">Number of tables (min: 1)</label>
            <input type="number" class="form-control" id="tables" name="tables" min="1" value="1" required>
        </div>
        <div class="form-group">
            <label for="max_people">Maximum amount of people (min: 1)</label>
            <input type="number" class="form-control" id="max_people" name="max_people" min="1" value="1" required>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Submit</button> (all fields are required)
    </form>
</div>
@stop