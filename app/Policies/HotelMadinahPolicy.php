<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HotelMadinah;
use Illuminate\Auth\Access\HandlesAuthorization;

class HotelMadinahPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_hotel::madinah');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HotelMadinah $hotelMadinah): bool
    {
        return $user->can('view_hotel::madinah');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_hotel::madinah');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HotelMadinah $hotelMadinah): bool
    {
        return $user->can('update_hotel::madinah');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HotelMadinah $hotelMadinah): bool
    {
        return $user->can('delete_hotel::madinah');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_hotel::madinah');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, HotelMadinah $hotelMadinah): bool
    {
        return $user->can('force_delete_hotel::madinah');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_hotel::madinah');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, HotelMadinah $hotelMadinah): bool
    {
        return $user->can('restore_hotel::madinah');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_hotel::madinah');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, HotelMadinah $hotelMadinah): bool
    {
        return $user->can('replicate_hotel::madinah');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_hotel::madinah');
    }
}
