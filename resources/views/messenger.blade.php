@extends('layouts.app')

@section('content')
    <div class="pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <a href="/messenger" class="pl-6 py-4 pr-6 text-lg inline-block border-r border-gray-200 bg-white hover:bg-gray-200 transition duration-200">Back</a>
                <div class="mx-4 my-4 text-lg inline-block">{{ $recipient_name }}</div>
                <div class="bg-gray-100 border sm:rounded-b-lg border-gray-200 overflow-auto" id="scroll" style="height: 600px;">
                    <div id="notification-container"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form class="flex">
                @csrf
                <input type="hidden" name="recipient" id="recipient" value="{{ $recipient_id }}">
                <input type="text" id="message" name="message" class="p-4 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Write here any text you want to send."/>
                <button type="submit" class="btn-submit inline-flex justify-center p-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-900 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ml-4 duration-200">Send</button>
            </form>
        </div>
    </div>

    <div class="fixed hidden inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="my-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div id="round" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="height: 40px;width: 40px;">
                        <line x1="10" y1="10" x2="30" y2="30" stroke="red" stroke-width="5" />
                        <line x1="30" y1="10" x2="10" y2="30" stroke="red" stroke-width="5" />
                    </svg>
                </div>
                <h3 class="mt-2 text-lg leading-6 font-medium text-gray-900" id="modal-title"></h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="modal-text"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="ok-btn" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 duration-200">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        function renderMessage(message, date, owner) {
            let style = "mr-auto";

            if (owner) {
                style = "ml-auto";
            }
            notification_container.innerHTML += "<div class=\"p-4 m-4 " + style + " bg-indigo-50 rounded-xl shadow-sm border border-gray-300 w-96\"><p class=\"text-black\">" + message + "</p><p class=\"text-gray-400 text-xs\">" + date + "</p></div>";
            scroll.scrollTop = scroll.scrollHeight;
        }

        let modal = document.getElementById("my-modal");
        let button = document.getElementById("ok-btn");
        let notification_container = document.getElementById("notification-container");
        let scroll = document.getElementById("scroll");

        button.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }

        document.addEventListener("DOMContentLoaded", ready);

        function ready() {

            Echo.private('message.to.{{ $user_id }}')
                .listen(".NewMessageEvent", e => {
                    renderMessage(e.message, e.datetime, false);
                    console.log(e);
                })
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".btn-submit").click(function(e){

            e.preventDefault();

            const recipient = $("input[name=recipient]").val();
            const message = $("input[name=message]").val();

            $.ajax({
                type:'POST',
                url:"{{ route('message-send') }}",
                data:{recipient:recipient, message:message},
                success:function(data){
                    //document.getElementById("modal-text").innerHTML = data;
                    //document.getElementById("modal-title").innerHTML = "Successful!";
                    //modal.style.display = "block";
                },
                error:function(data){
                    document.getElementById("modal-text").innerHTML = data.responseJSON.message;
                    document.getElementById("modal-title").innerHTML = "Error!";
                    modal.style.display = "block";
                }
            });
            if (message) renderMessage(message, new Date().toISOString().slice(0, 19).replace('T', ' '), true);
        });

        @foreach($messages as $message)
        renderMessage("{{ $message['message'] }}", "{{ $message['sent_at'] }}", {{ $message['sender'] === $user_id }});
        @endforeach
    </script>
@endsection
