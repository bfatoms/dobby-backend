<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, $order_type = 'INV')
    {
        if ($order_type == 'BILL') {
            return $user->isAllowedTo('purchases-bills', 'list');
        }
        return $user->isAllowedTo('sales-invoices', 'list');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user, $order_type = 'INV')
    {
        if ($order_type == 'BILL') {
            return $user->isAllowedTo('purchases-bills', 'show');
        }
        return $user->isAllowedTo('sales-invoices', 'show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user, $order_type = 'INV')
    {
        if ($order_type == 'BILL') {
            return $user->isAllowedTo('purchases-bills', 'create');
        }
        return $user->isAllowedTo('sales-invoices', 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, $order_type = 'INV')
    {
        if ($order_type == 'BILL') {
            return $user->isAllowedTo('purchases-bills', 'update');
        }
        return $user->isAllowedTo('sales-invoices', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, $order_type = 'INV')
    {
        if ($order_type == 'BILL') {
            return $user->isAllowedTo('purchases-bills', 'trash');
        }
        return $user->isAllowedTo('sales-invoices', 'trash');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function restore(User $user, $order_type = 'INV')
    {
        if ($order_type == 'BILL') {
            return $user->isAllowedTo('purchases-bills', 'restore');
        }
        return $user->isAllowedTo('sales-invoices', 'restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, $order_type = 'INV')
    {
        if ($order_type == 'BILL') {
            return $user->isAllowedTo('purchases-bills', 'force-delete');
        }
        return $user->isAllowedTo('sales-invoices', 'force-delete');
    }
}
