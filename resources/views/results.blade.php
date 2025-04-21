@extends('layouts.app')

@section('title', 'Fauna Results')

@section('content')
    <h1>Fauna Details</h1>
    @foreach ($faunas as $fauna)
        <div>
            <h2>{{ $fauna->nameId }}</h2>
            <p>{{ $fauna->family }}</p>
        </div>
    @endforeach
@endsection
