<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{
    IChat,
    IComment,
    IDesign,
    IInvitation,
    IMessage,
    ITeam,
    IUser
};
use App\Repositories\Eloquent\{
    ChatRepository,
    CommentRepository,
    DesignRepository,
    InvitationRepository,
    MessageRepository,
    TeamsRepository,
    UserRepository
};
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(IDesign::class,DesignRepository::class);
        $this->app->bind(IUser::class,UserRepository::class);
        $this->app->bind(IComment::class, CommentRepository::class);
        $this->app->bind(ITeam::class, TeamsRepository::class);
        $this->app->bind(IInvitation::class, InvitationRepository::class);
        $this->app->bind(IChat::class, ChatRepository::class);
        $this->app->bind(IMessage::class, MessageRepository::class);
    }
}
