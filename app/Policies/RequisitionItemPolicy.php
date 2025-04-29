<?php

namespace App\Policies;

use App\Models\RequisitionItem;
use App\Models\User;
use App\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequisitionItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any requisition items.
     */
    public function viewAny(User $user, Event $event): bool
    {
        // If the requisition list is public or the user is invited, they can view items
        return $event->requisitionList->is_public || $event->requisitionList->users->contains($user);
    }

    /**
     * Determine whether the user can view the requisition item.
     */
    public function view(User $user, RequisitionItem $requisitionItem): bool
    {
        $event = $requisitionItem->requisitionList->event;

        // User can view the item if they can view the requisition list
        return $this->viewAny($user, $event);
    }

    /**
     * Determine whether the user can create requisition items.
     */
    public function create(User $user, Event $event): bool
    {
        // Creator can always add items, or if it's for another user, both creator and "event for" can add
        return $event->created_by === $user->id || $event->event_for === $user->id;
    }

    /**
     * Determine whether the user can update the requisition item.
     */
    public function update(User $user, RequisitionItem $requisitionItem): bool
    {
        $event = $requisitionItem->requisitionList->event;

        // An item can only be updated by the event creator, the "event for" user, or the user who created the item
        return ($event->created_by === $user->id || $event->event_for === $user->id) && !$requisitionItem->claimed;
    }

    /**
     * Determine whether the user can delete the requisition item.
     */
    public function delete(User $user, RequisitionItem $requisitionItem): bool
    {
        $event = $requisitionItem->requisitionList->event;

        // Only event creator or the "event for" user can delete items
        return $event->uscreated_byer_id === $user->id || $event->event_for === $user->id;
    }


    public function claim(User $user, RequisitionItem $requisitionItem): bool
    {
        if($requisitionItem->availability == false) {
            return false;
        }
        $event = $requisitionItem->requisitionList->event;

        $pivotStatus = $event->users->find($user->id)?->pivot->status;

        return $pivotStatus === 1;
    }

    /**
     * Determine whether the user can restore the requisition item.
     */
    public function restore(User $user, RequisitionItem $requisitionItem): bool
    {
        // Return false as restore doesn't apply in this case
        return false;
    }

    /**
     * Determine whether the user can permanently delete the requisition item.
     */
    public function forceDelete(User $user, RequisitionItem $requisitionItem): bool
    {
        // Return false as permanent deletion is not allowed in this case
        return false;
    }
}
