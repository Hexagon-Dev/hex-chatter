@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p class="text-center text-lg mb-4">Choose chat to start messaging:</p>
                    @foreach($data as $user)
                        <a href="/messenger/{{ $user['name'] }}">
                            <div class="border border-gray-300 hover:bg-gray-100 transition duration-300 mb-4 p-4 mx-auto bg-white rounded-xl shadow-lg space-y-2">
                                <p class="text-lg text-black font-semibold inline-block">{{ $user['name'] }}</p>
                                <p class="text-lg text-gray-500 font-semibold float-right">{{ $user['date'] }}</p>
                                <p class="text-lg text-gray-500 font-semibold m-0">{{ $user['message'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
