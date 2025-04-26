<?php

use Livewire\Volt\Component;
use App\Models\Event;
use App\Models\EventType;
use App\Models\RequisitionItem;
use App\Models\User;
use Illuminate\Support\Carbon;

new class extends Component {
    public Event $event;
    public string $title;
    public string $date;
    public string $time;
    public ?int $event_type_id;
    public string $event_guidelines;
    public ?int $event_for;
    public $confirmingDelete = false;
    public bool $eventExpired = false;

    public function mount(Event $event)
    {
        $this->event = $event->load(['requisitionList.requisitionItems', 'users']);
        $this->eventExpired = $this->event->hasExpired();
        $this->title = $event->title;
        $this->date = $event->date ? Carbon::parse($event->date)->format('Y-m-d') : '';
        $this->time = $event->time ? Carbon::parse($event->time)->format('H:i') : '';
        $this->event_type_id = $event->event_type_id;
        $this->event_guidelines = $event->event_guidelines ?? '';
        $this->event_for = $event->event_for;
    }

    public function getEventTypeProperty(): ?EventType
    {
        return $this->event_type_id ? EventType::find($this->event_type_id) : null;
    }

    public function getEventForUserProperty(): ?User
    {
        return $this->event_for ? User::find($this->event_for) : null;
    }

    public function getInvitedUsersProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->event->users;
    }

    public function acceptEvent()
    {
        $this->event->users()->updateExistingPivot(auth()->id(), ['status' => 1]);
        $this->event = $this->event->fresh('users');
    }

    public function declineEvent()
    {
        $this->event->users()->updateExistingPivot(auth()->id(), ['status' => 0]);
        $this->event = $this->event->fresh('users');
    }

    public function claimItem($itemId)
    {
        $item = RequisitionItem::findOrFail($itemId);

        if ($item->claimed_by) {
            return;
        }

        $this->authorize('claim', $item);

        $requisitionList = $item->requisitionList;

        $isInvited = $this->event->users->contains(auth()->id());
        $canClaim = $requisitionList->visibility == 1 || $isInvited;

        if ($canClaim) {
            $item->claimed_by = auth()->id();
            $item->save();
            $this->event = $this->event->fresh('requisitionList.requisitionItems');
        }
    }

    public function deleteEvent()
    {
        $this->authorize('delete', $this->event);

        $this->event->delete();

        session()->flash('message', 'Event deleted successfully.');
        return redirect()->route('events.index');
    }

    public function confirmDelete()
    {
        $this->confirmingDelete = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-2xl font-semibold mb-4">{{ $title }}</h1>

                <div class="mb-4">
                    <p class="text-gray-600 dark:text-gray-400">
                        <span class="font-semibold">Date:</span> {{ $date }}
                    </p>
                    <p class="text-gray-600 dark:text-gray-400">
                        <span class="font-semibold">Time:</span> {{ $time }}
                    </p>
                </div>

                @if ($this->eventType)
                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">Event Type:</span> {{ $this->eventType->name }}
                        </p>
                    </div>
                @endif

                @if ($this->eventForUser)
                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">Event For:</span> {{ $this->eventForUser->name }}
                        </p>
                    </div>
                @endif

                @if ($event_guidelines)
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Guidelines</h2>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $event_guidelines }}</p>
                    </div>
                @endif

                @if ($this->invitedUsers->isNotEmpty())
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Invited Users</h2>
                        <ul class="list-disc list-inside">
                            @foreach ($this->invitedUsers as $user)
                                <li class="text-gray-600 dark:text-gray-400">{{ $user->name }} ({{ $user->email }})
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($this->event->requisitionList && $this->event->requisitionList->requisitionItems->isNotEmpty())
                    <div class="mt-8">
                        @php
                            $user = auth()->user();
                            $isInvited = $this->invitedUsers->contains($user);
                            $canClaim = $this->event->requisitionList->visibility == 1 || $isInvited;

                        @endphp
                        @if ($canClaim)
                            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Requisition Items
                            </h2>

                            <ul class="space-y-4">
                                @foreach ($this->event->requisitionList->requisitionItems as $item)
                                    <li class="bg-gray-100 dark:bg-gray-700 p-4 rounded shadow-sm">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-base font-medium text-gray-800 dark:text-gray-100">
                                                    {{ $item->name }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $item->is_optional ? 'Optional' : 'Required' }}
                                                </p>
                                                @if ($item->claimed_by)
                                                    <p class="text-xs text-green-600 dark:text-green-400">Claimed by:
                                                        {{ \App\Models\User::find($item->claimed_by)?->name }}</p>
                                                @endif
                                            </div>

                                            @php
                                                $alreadyClaimed = $item->claimed_by === $user->id;
                                            @endphp

                                            @if (!$this->eventExpired)
                                                @if ($item->claimed_by === null && $canClaim)
                                                    <x-primary-button wire:click="claimItem({{ $item->id }})">
                                                        {{ __('Claim') }}
                                                    </x-primary-button>
                                                @elseif ($alreadyClaimed)
                                                    <span class="text-green-600 font-semibold text-sm">You claimed
                                                        this</span>
                                                @endif
                                            @else
                                                <span class="text-gray-500 font-semibold text-sm">Event Expired</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach

                        @endif
                        </ul>
                    </div>
                @endif


                <div class="mt-6 flex items-center gap-4">
                    @can('update', $event)
                        <a href="{{ route('events.edit', $event) }}">
                            <x-primary-button>
                                {{ __('Edit Event') }}
                            </x-primary-button>
                        </a>
                    @endcan
                    @can('delete', $event)
                        <x-danger-button wire:click="confirmDelete">
                            {{ __('Delete Event') }}
                        </x-danger-button>
                    @endcan


                    @can('create', [RequisitionItem::class, $event])
                        @if ($this->event->requisitionList)
                            <a
                                href="{{ route('requisition-items.create', ['requisition_list' => $this->event->requisitionList->id]) }}">
                                <x-primary-button>
                                    {{ __('Add Requisition Item') }}
                                </x-primary-button>
                            </a>
                        @endif
                    @endcan


                    <a href="{{ route('events.gallery', ['event' => $this->event]) }}">
                        <x-primary-button>
                            {{ __('Events Gallery') }}
                        </x-primary-button>
                    </a>


                    <a href="{{ route('events.index') }}">
                        <x-secondary-button>
                            {{ __('Back to Events') }}
                        </x-secondary-button>
                    </a>
                    @auth
                        @php
                            $user = auth()->user();
                            $isInvited = $this->invitedUsers->contains($user);
                            $isEventCreator = $event->user_id === $user->id;
                            $pivotStatus = $isInvited ? $this->event->users->find($user->id)?->pivot->status : null;
                        @endphp

                        @if ($isInvited && !$isEventCreator)
                            @if (is_null($pivotStatus))
                                <div class="flex gap-2">
                                    <x-primary-button wire:click="acceptEvent">
                                        {{ __('Accept Invitation') }}
                                    </x-primary-button>
                                    <x-danger-button wire:click="declineEvent">
                                        {{ __('Decline Invitation') }}
                                    </x-danger-button>
                                </div>
                            @elseif ($pivotStatus === 1)
                                <p class="text-green-600 font-semibold">You have accepted this invitation.</p>
                            @elseif ($pivotStatus === 0)
                                <p class="text-red-600 font-semibold">You have declined this invitation.</p>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
    @if ($confirmingDelete)
        <x-modal name="confirm-event-deletion" :show="$confirmingDelete" focusable>
            <form wire:submit="deleteEvent" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Are you sure you want to delete this event?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Once deleted, all event details will be permanently removed.') }}
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="cancelDelete">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        {{ __('Delete Event') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    @endif
</div>
