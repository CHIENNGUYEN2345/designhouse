<?php

namespace App\Repositories\Eloquent;

use App\Models\Chat;
use App\Repositories\Contracts\IChat;
use App\Repositories\Eloquent\BaseRepository;

class ChatRepository extends BaseRepository implements IChat
{
    public function model(){
        return Chat::class; //App\Models\Designs
    }
    
    public function createParticipants($chatId, array $data)
    {
        $chat = $this->model->find($chatId);

        $chat->participants()->sync($data);
    }
    public function getUserChats(){
        return auth()->user()->chats;
    }
}