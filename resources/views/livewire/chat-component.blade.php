<div>
    <div style="overscroll-behavior: none;">
        <div class="fixed w-full bg-green-400 h-16 pt-2 text-white flex items-center shadow-md" style="top: 0;">
            <a href="/dashboard" class="ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-12 h-12 my-1 text-green-100">
                    <path class="text-green-100 fill-current" d="M9.41 11H17a1 1 0 0 1 0 2H9.41l2.3 2.3a1 1 0 1 1-1.42 1.4l-4-4a1 1 0 0 1 0-1.4l4-4a1 1 0 0 1 1.42 1.4L9.4 11z" />
                </svg>
            </a>
            <div class="flex-grow text-center text-green-100 font-bold text-lg tracking-wide">{{ $user->name }}</div>
        </div>

        <div class="mt-20 mb-16">
            @foreach($messages as $message)
                <div class="clearfix w-full">
                    @if($message['sender'] != auth()->user()->name)
                        <div class="bg-gray-300 mx-4 my-2 p-2 rounded-lg inline-block">
                            @if($message['reply_to'])
                                <div class="text-gray-600 text-sm">Replying to: {{ $message['reply_to'] }}</div>
                            @endif
                            {{ $message['message'] }}
                            <button wire:click="setReply({{ $message['id'] }})" class="text-blue-500 text-sm ml-2">Reply</button>
                        </div>
                    @else
                        <div class="text-right">
                            <div class="bg-green-300 mx-4 my-2 p-2 rounded-lg inline-block">
                                @if($message['reply_to'])
                                    <div class="text-gray-600 text-sm">Replying to: {{ $message['reply_to'] }}</div>
                                @endif
                                {{ $message['message'] }}
                                </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <form wire:submit.prevent="sendMessage">
        <div class="fixed w-full flex flex-col bg-green-100" style="bottom: 0;">
            @if($replyToMessageId && $replyMessagePreview)
                <div class="bg-gray-200 p-2 rounded-lg mb-1">
                    <span>Replying to: </span><strong>{{ $replyMessagePreview }}</strong>
                    <button wire:click="cancelReply" class="text-red-500 text-sm ml-2">Cancel</button>
                </div>
            @endif
            <div class="flex justify-between">
                <textarea
                    class="flex-grow m-2 py-2 px-4 border border-gray-300 bg-gray-200 resize-none"
                    rows="1"
                    wire:model="message"
                    placeholder="Message..."
                    style="outline: none;"
                ></textarea>
                <button class="m-2 flex items-center" type="submit" style="outline: none;">
                    <svg class="svg-inline--fa text-green-400 fa-paper-plane fa-w-16 w-8 h-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="paper-plane" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="currentColor" d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z" />
                    </svg>
                </button>
            </div>
        </div>
    </form>
</div>
