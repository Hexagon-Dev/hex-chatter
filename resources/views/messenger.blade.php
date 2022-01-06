@extends('layouts.app')

@section('content')
    <div class="pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <a href="{{ route('users') }}" class="pl-6 py-4 pr-6 text-lg inline-block border-r border-gray-200 bg-white hover:bg-gray-200 transition duration-200">Back</a>
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

    <script>
        function renderMessage(message, date, sender, owner) {
            let style = "mr-auto";

            if (owner) style = "ml-auto";

            notification_container.innerHTML += "<div class=\"p-4 m-4 " + style + " bg-indigo-50 rounded-xl shadow-sm border border-gray-300 w-96\"><p class=\"mb-2 font-bold\">" + sender + "</p><p class=\"text-black mb-2\">" + message + "</p><p class=\"text-gray-400 text-xs\">" + date + "</p></div>";
            scroll.scrollTop = scroll.scrollHeight;
        }

        let notification_container = document.getElementById("notification-container");
        let scroll = document.getElementById("scroll");

        document.addEventListener("DOMContentLoaded", ready);

        function ready() {

            if ("{{ $is_group }}" == 1) {
                Echo.channel('message.group.{{ $recipient_id }}')
                    .listen(".NewMessageEvent", e => {
                        if (e.sender_id !== {{ Auth::id() }}) renderMessage(e.message, e.datetime, e.sender, false);
                        else renderMessage(e.message, e.datetime, e.sender, true);
                        console.log(e);
                    });
            } else {
                Echo.private('message.to.{{ $user_id }}')
                    .listen(".NewMessageEvent", e => {
                        renderMessage(e.message, e.datetime, e.sender, false);
                        console.log(e);
                    });
            }
        }


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
            if ("{{ $is_group }}" == 1) {
                if (message.groupq) renderMessage(message, new Date().toISOString().slice(0, 19).replace('T', ' '), "{{ Auth::user()->name }}", true);
            } else {
                if (message) renderMessage(message, new Date().toISOString().slice(0, 19).replace('T', ' '), "{{ Auth::user()->name }}", true);
            }
            $('#message').val('');
        });

        @foreach($messages as $message)
        renderMessage("{{ $message['message'] }}", "{{ $message['sent_at'] }}", "{{ $message['sender_name'] }}", {{ $message['sender'] === $user_id }});
        @endforeach
    </script>
@endsection
