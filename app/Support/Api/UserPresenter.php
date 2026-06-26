<?php

namespace App\Support\Api;

use App\Models\User;

class UserPresenter
{
    public static function profile(User $user): array
    {
        $user->loadMissing(['nest', 'group']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'birthday' => $user->birthday,
            'gender' => $user->gender,
            'address' => $user->address,
            'image' => $user->image,
            'nest_id' => $user->nest_id,
            'nest_name' => $user->nest?->name,
            'group_id' => $user->group_id,
            'group_name' => $user->group?->name,
        ];
    }

    public static function listItem(User $user): array
    {
        $user->loadMissing(['nest', 'group']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'nest_name' => $user->nest?->name,
            'group_name' => $user->group?->name,
        ];
    }
}
