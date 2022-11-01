<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Transaction;

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{convId}', function ($user, $convId) {
    return [
        'id' => $user->id,
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'dp' => $user->dp,
        'role' => $user->role,
        'status' => $user->status
    ];
});

Broadcast::channel('last-message', function ($user) {
    return [
        'id' => $user->id,
        'first_name' => $user->first_name
    ];
});

/* New transaction has been created by a user */
Broadcast::channel('transaction.{id}', function ($user, $id) {
    return $user->id == $id;
    /* return true; */
});

/* Users personal channel to recieve notifications */
Broadcast::channel('user.{id}', function($user, $id){
    /* return true; */
    return $user->id == $id;
});
