<?php

use App\Models\TodoListItem;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('create.item.notify.user.{id}', function (User $user, int $id) {
    return (int) $user->id === (int) TodoListItem::find($id)->user_id;
});
