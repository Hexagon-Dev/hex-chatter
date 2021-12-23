@extends('layouts.app')

@section('content')
    <main class="sm:container sm:mx-auto sm:mt-10">
        <div class="w-full sm:px-6">
            <div class="bg-gray-100">
                <div class="container mx-auto flex flex-col items-center py-24 sm:py-36">
                    <div class="w-11/12 sm:w-2/3 lg:flex justify-center items-center flex-col  mb-5 sm:mb-10">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl text-center text-gray-800 font-black leading-7 md:leading-10">
                            <span class="text-indigo-700">Awesome</span>
                            chatting is here!
                        </h1>
                        <p class="mt-5 sm:mt-10 lg:w-10/12 text-gray-400 font-normal text-center text-sm sm:text-lg">Tired of endless message loading? Here we are! Web-Socket base chatting app <span class="text-indigo-700">Hex-Chatter</span> will make your life much easier. Simply register by pressing button below and start chatting right now!</p>
                    </div>
                    <div class="flex justify-center items-center">
                        <a href="/register" class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-700 bg-indigo-700 transition duration-150 ease-in-out hover:bg-indigo-600 lg:text-xl lg:font-bold  rounded text-white px-4 sm:px-10 border border-indigo-700 py-2 sm:py-4 text-sm">Register</a>
                        <a href="/login"  class="ml-4 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-700 bg-transparent transition duration-150 ease-in-out hover:border-indigo-600 lg:text-xl lg:font-bold  hover:text-indigo-600 rounded border border-indigo-700 text-indigo-700 px-4 sm:px-10 py-2 sm:py-4 text-sm">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
