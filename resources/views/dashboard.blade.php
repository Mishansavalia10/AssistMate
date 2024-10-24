<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Chats</h2> <!-- Adjusted margin-bottom -->

                    @foreach($users as $user)
                        <div class="flex items-center border-b border-gray-200 py-3">
                            <div class="w-12 h-12 rounded-full bg-gray-300 mr-3"></div> <!-- Placeholder for user avatar -->
                            <div class="flex-grow">
                                <a href="{{ route('chat', $user->id) }}" class="text-gray-900 font-medium hover:text-green-600">
                                    {{ $user->name }}
                                </a>
                                <!-- <p class="text-gray-600 text-sm">Last message here...</p> Placeholder for last message -->
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
