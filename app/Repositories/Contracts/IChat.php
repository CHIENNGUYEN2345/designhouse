<?php

namespace App\Repositories\Contracts;

interface IChat{
    // public function getChatWithUser(){

    // }

    public function createParticipants($chatId, array $data);
    
}