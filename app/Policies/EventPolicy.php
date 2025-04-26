<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Any authenticated user can view their own events.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Users can view the event if they created it or are the 'event_for' user.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->id === $event->created_by || $user->id === $event->event_for || $event->users->contains($user);
    }

    /**
     * Any logged-in user can create an event.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the creator of the event can update it.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->id === $event->created_by;
    }

    /**
     * Only the creator can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->id === $event->created_by;
    }

    public function restore(User $user, Event $event): bool
    {
        return false;
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return false;
    }
}
