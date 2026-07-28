<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;

class ImagePolicy
{
    public function update(User $user, Image $image): bool
    {
        return $user->can('update', $image->property);
    }

    public function delete(User $user, Image $image): bool
    {
        return $user->can('update', $image->property);
    }
}
