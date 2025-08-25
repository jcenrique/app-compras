<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Market;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny( $user): bool
    {
        return $user->can('view_any_market');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view( $user, Market $market): bool
    {
        return $user->can('view_market');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create( $user): bool
    {
        return $user->can('create_market');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update( $user, Market $market): bool
    {
        return $user->can('update_market');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete( $user, Market $market): bool
    {
        return $user->can('delete_market');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny( $user): bool
    {
        return $user->can('delete_any_market');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete( $user, Market $market): bool
    {
        return $user->can('force_delete_market');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny( $user): bool
    {
        return $user->can('force_delete_any_market');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore( $user, Market $market): bool
    {
        return $user->can('restore_market');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_market');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate( $user, Market $market): bool
    {
        return $user->can('replicate_market');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder( $user): bool
    {
        return $user->can('reorder_market');
    }
}
