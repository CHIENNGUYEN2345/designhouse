<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    protected function verificationUrl($notifiable)
    {
        $appUrl = config('app.client_url', config('app.url'));

        $url = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), ['user'=>$notifiable->id]
        );
        
        //http://designhouse.test/api/abc123xyzilikeitaoa
        return str_replace(url('/api'), $appUrl, $url);
    }
}
