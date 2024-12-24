@extends('layouts.app')

@section('title', 'Attractions List')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Attractions</h1>

    <div class="mb-6">
        <form action="{{ route('attractions.index') }}" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="location" class="px-4 py-2 border rounded-md" placeholder="Filter by location" value="{{ request('location') }}">
            <input type="number" name="min_price" class="px-4 py-2 border rounded-md" placeholder="Min price" value="{{ request('min_price') }}">
            <input type="number" name="max_price" class="px-4 py-2 border rounded-md" placeholder="Max price" value="{{ request('max_price') }}">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @foreach($attractions as $attraction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $attraction->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $attraction->location }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${{ number_format($attraction->price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('attractions.show', $attraction) }}" class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                        @admin
                        <a href="{{ route('attractions.edit', $attraction) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</a>
                        <form action="{{ route('attractions.destroy', $attraction) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this attraction?')">Delete</button>
                        </form>
                        @endadmin
                    </td>
                </tr>
            @endforeach
            @if($attractions->isEmpty())
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap" colspan="4">No attractions found.</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $attractions->links() }}
    </div>

    <div id="new-attraction-notification" class="hidden fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded-md shadow-lg" role="alert">
        <div class="flex">
            <div class="py-1"><svg class="fill-current h-6 w-6 text-white mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
            <div>
                <p class="font-bold">New Attraction Added</p>
                <p class="text-sm" id="new-attraction-message"></p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            var channel = pusher.subscribe('attractions');
            channel.bind('attraction.created', function(data) {
                var message = 'New attraction "' + data.name + '" added in ' + data.location;
                $('#new-attraction-message').text(message);
                $('#new-attraction-notification').removeClass('hidden').addClass('flex');
                setTimeout(function() {
                    $('#new-attraction-notification').removeClass('flex').addClass('hidden');
                }, 5000);
            });
        });
    </script>
@endsection
