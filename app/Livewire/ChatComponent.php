<?php

namespace App\Livewire;

use App\Events\MessageSendEvent;
use App\Models\Message;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatComponent extends Component
{
    public $user;
    public $sender_id;
    public $receiver_id;
    public $message = '';
    public $messages = [];
    public $replyToMessageId = null;
    public $replyMessagePreview = null; // Add thi

    public function render()
    {
        return view('livewire.chat-component');
    }

    public function mount($user_id){

        $this->sender_id = auth()->user()->id;
        $this->receiver_id = $user_id;

        $messages = Message::where(function($query){
            $query->where('sender_id', $this->sender_id)
                  ->where('receiver_id', $this->receiver_id);
        })->orWhere(function($query){
            $query->where('sender_id', $this->receiver_id)
                  ->where('receiver_id', $this->sender_id);
        })
        ->with('sender:id,name', 'receiver:id,name')
        ->get();

        foreach($messages as $message){
            $this->appendChatMessage($message);
        }

        $this->user = User::whereId($user_id)->first();
    }

    

    public function sendMessage()
    {
    $chatMessage = new Message();
    $chatMessage->sender_id = $this->sender_id;
    $chatMessage->receiver_id = $this->receiver_id;
    $chatMessage->message = $this->message;

    if ($this->replyToMessageId) {
        $chatMessage->reply_to = $this->replyToMessageId;
        $this->replyToMessageId = null; // Reset after sending
    }
    
    $chatMessage->save();
    $this->appendChatMessage($chatMessage); // Ensure it's appended to your local messages

    // Send the auto-reply if applicable
    $autoReply = $this->getAutoReply($this->message);
    if ($autoReply) {
        $autoReplyMessage = new Message();
        $autoReplyMessage->sender_id = $this->receiver_id; // Assuming the bot sends as the receiver
        $autoReplyMessage->receiver_id = $this->sender_id;
        $autoReplyMessage->message = $autoReply;
        $autoReplyMessage->save();
        $this->appendChatMessage($autoReplyMessage); // Append auto-reply
        broadcast(new MessageSendEvent($autoReplyMessage))->toOthers();
    }

    broadcast(new MessageSendEvent($chatMessage))->toOthers();
    $this->message = '';
    $this->replyMessagePreview = null; // Clear the reply preview
    }

    #[On('echo-private:chat-channel.{sender_id},MessageSendEvent')]
    public function listenForMessage($event){
        $chatMessage = Message::whereId($event['message']['id'])
            ->with('sender:id,name', 'receiver:id,name')
            ->first();

        $this->appendChatMessage($chatMessage);
    }

    public function appendChatMessage($message)
    {
    $this->messages[] = [
        'id' => $message->id,
        'message' => $message->message,
        'sender' => $message->sender->name,
        'receiver' => $message->receiver->name,
        'reply_to' => $message->reply_to ? Message::find($message->reply_to)->message : null,
    ];
    }

    // autoreply
    private function getAutoReply($message)
    {
        $responses = [
            'hello' => 'Hello! How can I help you today?',
            'help' => 'Sure! What do you need help with?',
            'hi' => 'Hi there! How can I assist you today?',
            'test' => 'This is test message for testing purposes.',
            '' => 'please type valid message',
        ];

        foreach ($responses as $keyword => $response) {
            if (stripos($message, $keyword) !== false) {
                return $response;
            }
        }

        return 'This feature is currently under maintenance. Please try again later.'; // No auto-reply found
    }
    // Method to set the reply context
    public function setReply($messageId)
    {
        $message = Message::find($messageId);
        if ($message) {
            $this->replyToMessageId = $messageId;
            $this->replyMessagePreview = $message->message; // Store the message text for preview
            $this->message = ''; // Optionally clear the input message
        }
    }

public function cancelReply()
{
    $this->replyToMessageId = null; // Reset reply context
    $this->replyMessagePreview = null; // Clear the preview
}
}