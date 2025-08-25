<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        return $user->can('view_any_product');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Product $product): bool
    {
        return $user->can('view_product');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        return $user->can('create_product');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Product $product): bool
    {
        return $user->can('update_product');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Product $product): bool
    {
        return $user->can('delete_product');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny($user): bool
    {
        return $user->can('delete_any_product');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete($user, Product $product): bool
    {
        return $user->can('force_delete_product');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny($user): bool
    {
        return $user->can('force_delete_any_product');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore($user, Product $product): bool
    {
        return $user->can('restore_product');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny($user): bool
    {
        return $user->can('restore_any_product');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate($user, Product $product): bool
    {
        return $user->can('replicate_product');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder($user): bool
    {
        return $user->can('reorder_product');
    }
}
