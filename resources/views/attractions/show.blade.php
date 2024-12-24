@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-4">{{ $attraction->name }}</h1>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <strong class="block text-gray-700 text-sm font-bold mb-2">Description:</strong>
                <p>{{ $attraction->description }}</p>
            </div>

            <div class="mb-4">
                <strong class="block text-gray-700 text-sm font-bold mb-2">Location:</strong>
                <p>{{ $attraction->location }}</p>
            </div>

            <div class="mb-4">
                <strong class="block text-gray-700 text-sm font-bold mb-2">Price:</strong>
                <p>${{ number_format($attraction->price, 2) }}</p>
            </div>

            @if($attraction->image)
                <div class="mb-4">
                    <strong class="block text-gray-700 text-sm font-bold mb-2">Image:</strong>
                    <img src="{{ asset('storage/' . $attraction->image) }}" alt="{{ $attraction->name }}" class="max-w-xs">
                </div>
            @endif

            @if(isset($weather))
                <div class="mb-4">
                    <strong class="block text-gray-700 text-sm font-bold mb-2">Current Weather in {{ $weather['location']['name'] }}:</strong>
                    <p>Temperature: {{ $weather['main']['temp'] }}°C</p>
                    <p>Conditions: {{ $weather['weather'][0]['description'] }}</p>
                    @if($weather['location']['state'])
                        <p>State: {{ $weather['location']['state'] }}</p>
                    @endif
                    <p>Country: {{ $weather['location']['country'] }}</p>
                </div>
            @elseif(isset($weatherError))
                <div class="mb-4 text-red-600">
                    <p>{{ $weatherError }}</p>
                </div>
            @endif

        </div>

        @admin
        <div class="flex space-x-4">
            <a href="{{ route('attractions.edit', $attraction) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</a>
            <form action="{{ route('attractions.destroy', $attraction) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this attraction?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
            </form>
        </div>
        @endadmin
    </div>
@endsection
