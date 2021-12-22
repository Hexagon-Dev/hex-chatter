<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messenger') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div id="app" class="mb-4">
                        <div class="content">
                            <div class="m-b-md">
                                New notification will be alerted realtime!
                            </div>
                        </div>
                    </div>

                    <form>
                        <div class="col-span-6 sm:col-span-3 mb-4">
                            <label for="recipient" class="block text-sm font-medium text-gray-700">Recipient</label>
                            <input type="text" name="recipient" id="recipient" autocomplete="given-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">
                                Message
                            </label>
                            <div class="mt-1">
                                <textarea id="message" name="message" rows="3" class="p-4 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Write here any text you want to send."></textarea>
                            </div>
                        </div>
                        <div class="py-3">
                            <button type="submit" class="btn-submit inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Send
                            </button>
                        </div>
                    </form>

                    <div class="fixed hidden inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="my-modal">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <div id="round" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                <svg
                                    class="h-6 w-6 bg-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                            </div>
                            <h3 class="mt-2 text-lg leading-6 font-medium text-gray-900" id="modal-title"></h3>
                            <div class="mt-2 px-7 py-3">
                                <p class="text-sm text-gray-500" id="modal-text"></p>
                            </div>
                            <div class="items-center px-4 py-3">
                                <button id="ok-btn" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                    </div>
                    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
                    <script src="{{ asset('js/app.js') }}" defer></script>
                    <script>
                        let modal = document.getElementById("my-modal");
                        let button = document.getElementById("ok-btn");

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
                            const message = $("textarea[name=message]").val();

                            $.ajax({
                                type:'POST',
                                url:"{{ route('message-send') }}",
                                data:{recipient:recipient, message:message},
                                success:function(data){
                                    document.getElementById("modal-text").innerHTML = data;
                                    document.getElementById("modal-title").innerHTML = "Successful!";
                                    modal.style.display = "block";
                                },
                                error:function(data){
                                    document.getElementById("modal-text").innerHTML = data.responseJSON.message;
                                    document.getElementById("modal-title").innerHTML = "Error!";
                                    modal.style.display = "block";
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
