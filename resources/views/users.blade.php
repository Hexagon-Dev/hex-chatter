@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 p-6 hidden" id="group-container">
                <form>
                    <label for="group_name" class="block text-sm font-medium text-gray-700">Write name of the group:</label>
                    <input type="text" name="group_name" class="mb-4 p-4 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-96 sm:text-sm border border-gray-300 rounded-md">
                    <button class="inline-flex justify-center p-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-900 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 duration-200 disabled:bg-blue-200 disabled:hover:bg-blue-200" id="create_group">Create</button>
                </form>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" id="box-container">
                    <div class="mb-4">
                        <button id="group" disabled class="inline-flex justify-center p-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-900 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 duration-200 disabled:bg-blue-200 disabled:hover:bg-blue-200">Create group</button>
                        <button id="delete" disabled class="inline-flex justify-center p-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-900 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 ml-4 duration-200 disabled:bg-red-200 disabled:hover:bg-red-200">Delete</button>
                        <span class="text-center text-lg mb-4 ml-4 text-gray-500">Choose chat to start messaging:</span>
                    </div>
                    <form>
                        @csrf
                        @foreach($data as $user)
                            @if($user['id'] === Auth::id())
                                @continue
                            @endif
                            <a href="/messenger/{{ $user['name'] }}">
                                <div class="border border-gray-300 hover:bg-gray-100 transition duration-300 mb-4 p-4 mx-auto bg-white rounded-xl shadow-lg flex space-between items-center">
                                    @if(!isset($user['is_group']))
                                        <input type="checkbox" id="{{ $user['id'] }}" name="user_id[]" value="{{ $user['id'] }}" class="mr-4 h-6 w-6">
                                    @else
                                        <div class="w-10"></div>
                                    @endif
                                    <div class="mr-auto">
                                        <p class="text-lg text-black font-semibold inline-block">{{ $user['name'] }}</p>
                                        <p class="text-lg text-gray-500 font-semibold">{{ $user['message'] }}</p>
                                    </div>
                                    <div><p class="text-lg text-gray-500 font-semibold">{{ $user['date'] }}</p></div>
                                </div>
                            </a>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('input:checkbox').click(function() {
            let total = $('#box-container').find('input[name="user_id[]"]:checked').length;

            if (total >= 1) $('#delete').removeAttr('disabled');
            else $('#delete').attr('disabled', 'disabled');

            if (total >= 2) $('#group').removeAttr('disabled');
            else $('#group').attr('disabled', 'disabled');
        });

        $('#delete').click(function(e) {
            let idArr = $("input:checkbox:checked").map(function(){ return $(this).val(); }).get();

            $.ajax({
                type: 'DELETE',
                url: "{{ route('conversation-delete') }}",
                data: {id_array: idArr},
                success: function () {
                    location.reload();
                },
                error: function (data) {
                    document.getElementById("modal-text").innerHTML = data.responseJSON.message;
                    document.getElementById("modal-title").innerHTML = "Error!";
                    modal.style.display = "block";
                }
            });
        });

        $('#group').click(function(e) {
            $('#group-container').removeClass('hidden');
            $('#group').attr('disabled', 'disabled');
            $('#delete').attr('disabled', 'disabled');
        });

        $('#create_group').click(function(e) {
            e.preventDefault();

            let idArr = $("input:checkbox:checked").map(function(){ return $(this).val(); }).get();
            let name = $("input[name=group_name]").val();
            $.ajax({
                type: 'POST',
                url: "{{ route('group-create') }}",
                data: {id_array: idArr, name:name},
                success: function () {
                    //location.reload();
                },
                error: function (data) {
                    document.getElementById("modal-text").innerHTML = data.responseJSON.message;
                    document.getElementById("modal-title").innerHTML = "Error!";
                    modal.style.display = "block";
                }
            });
        });
    </script>
@endsection
