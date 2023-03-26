<?php

use Illuminate\Support\Facades\Route;

//public routes
Route::get('me', 'User\MeController@getMe');

//get designs
Route::get('designs','Designs\DesignController@index');
Route::get('designs/{id}', 'Designs\DesignController@findDesign');
Route::get('designs/slug/{slug}', 'Designs\DesignController@findBySlug');

//get users
Route::get('users','User\UserController@index');
Route::get('users/{id}/designs','Designs\DesignController@getForUser');
Route::get('users/{username}','User\UserController@findByUsername');    

//user want to find team by slug, without log in.
Route::get('teams/slug/{slug}','Teams\TeamsController@findBySlug');
Route::get('teams/{id}/designs','Designs\DesignController@getForTeam');

//search
Route::get('search/designs','Designs\DesignController@search');



//route group for authenticated users only---------------------------
Route::group(['middleware'=>['auth:api']], function(){
    Route::post('logout','Auth\LoginController@logout');
    //update information of user
    Route::put('settings/profile','User\SettingsController@updateProfile');
    Route::put('settings/password','User\SettingsController@updatePassword');

    //upload design
    Route::post('designs', 'Designs\UploadController@upload');
    Route::put('designs/{id}', 'Designs\DesignController@update');
    Route::delete('designs/{id}', 'Designs\DesignController@destroy');
    // Route::get('designs/{id}', 'Designs\DesignController@findDesign');

    //comment
    Route::post('designs/{id}/comments', 'Designs\CommentController@store');
    Route::put('comments/{id}', 'Designs\CommentController@update');
    Route::delete('comments/{id}', 'Designs\CommentController@destroy');
    
    //LIKES and unlikes
    Route::post('designs/{id}/like','Designs\DesignController@like');
    Route::get('designs/{id}/liked','Designs\DesignController@checkIfUserHasLiked');

    //teams
    Route::post('teams','Teams\TeamsController@store');
    Route::get('teams/{id}','Teams\TeamsController@findById');
    Route::get('teams','Teams\TeamsController@index');
    Route::get('users/teams','Teams\TeamsController@fetchUserTeams');
    Route::put('teams/{id}','Teams\TeamsController@update');
    Route::delete('teams/{id}','Teams\TeamsController@destroy');
    Route::delete('teams/{team_id}/user/{user_id}','Teams\TeamsController@removeFromTeam');

    //invitation.
    Route::post('invitations/{teamId}','Teams\InvitationsController@invite');
    Route::post('invitations/{id}/resend','Teams\InvitationsController@resend');
    Route::post('invitations/{id}/respond','Teams\InvitationsController@respond');
    Route::delete('invitations/{id}/delete','Teams\InvitationsController@destroy');

    //chats
    Route::post('chats', 'Chats\ChatController@sendMessage');
    Route::get('chats', 'Chats\ChatController@getUserChats');
    Route::get('chats/{id}/messages', 'Chats\ChatController@getChatMessages');
    Route::put('chats/{id}/markasread', 'Chats\ChatController@markAsRead');
    Route::delete('message/{id}', 'Chats\ChatController@destroyMessage');
});


//route for guest api---------------------------------------------
Route::group(['middleware'=>['guest:api']], function(){
    Route::post('register','Auth\RegisterController@register');
    Route::post('verification/verify/{user}','Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend','Auth\VerificationController@resend');
    Route::post('login','Auth\LoginController@login');
    Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset','Auth\ResetPasswordController@reset')->name('password.reset');
    
    

});

