<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|date|after_or_equal:today')]
    public string $date = '';

    #[Validate('required|after_or_equal:now')]
    public string $time = '';

    #[Validate('required|exists:event_types,id')]
    public ?int $event_type_id = null;

    #[Validate('required|string')]
    public string $event_guidelines = '';

    #[Validate('required|exists:users,id')]
    public ?int $event_for = null;

    #[Validate('array|exists:users,id')]
    public array $invited_users = [];

    #[Validate('required|boolean')]
    public bool $visibility = false;

    public $users;
    public $eventTypes;

    public function mount()
    {
        $this->users = User::orderBy('name')->get();
        $this->eventTypes = EventType::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        // Create the event
        $event = Event::create([
            'title' => $this->title,
            'date' => $this->date,
            'time' => $this->time,
            'event_type_id' => $this->event_type_id,
            'event_guidelines' => $this->event_guidelines,
            'event_for' => $this->event_for,
            'created_by' => Auth::id(),
        ]);

        // Attach invited users to event
        $event->users()->attach($this->invited_users);

        // Create requisition list and set visibility
        $event->requisitionList()->create([
            'visibility' => $this->visibility, // Store visibility (0 for private, 1 for public)
        ]);

        // Flash success message and reset form
        session()->flash('success', 'Event created successfully!');
        $this->reset(['title', 'date', 'time', 'event_guidelines', 'event_for', 'invited_users', 'visibility']);

        // Redirect to event index
        redirect(route('events.index'));
    }
};

?>

<div class="max-w-xl mx-auto mt-10">
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input required wire:model="title" id="title" name="title" type="text" class="mt-1 block w-full"
                required autofocus autocomplete="title" />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>

        <div>
            <x-input-label for="date" :value="__('Date')" />
            <input required wire:model="date" id="date" name="date" type="date" class="mt-1 block w-full"
                required autocomplete="date" min="{{ \Carbon\Carbon::today()->toDateString() }}" />

            <x-input-error class="mt-2" :messages="$errors->get('date')" />
        </div>

        <div>
            <x-input-label for="time" :value="__('Time')" />
            <input required wire:model="time" id="time" name="time" type="time" class="mt-1 block w-full"
                required autocomplete="time" />
            <x-input-error class="mt-2" :messages="$errors->get('time')" />
        </div>

        <div>
            <x-input-label for="event_guidelines" :value="__('Event Guidelines')" />
            <textarea required wire:model="event_guidelines" id="event_guidelines" name="event_guidelines"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('event_guidelines')" />
        </div>

        <div>
            <x-input-label for="event_type_id" :value="__('Event Type')" />
            <select wire:model="event_type_id" id="event_type_id" name="event_type_id" class="mt-1 block w-full">
                <option value="">Select an event type</option>
                @foreach ($eventTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('event_type_id')" />
        </div>

        <div>
            <x-input-label for="event_for" :value="__('Event For (User)')" />
            <select wire:model="event_for" id="event_for" name="event_for" class="mt-1 block w-full">
                <option value="">Select a user</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('event_for')" />
        </div>

        <div>
            <x-input-label for="invited_users" :value="__('Invite Users')" />
            <div class="mt-2 space-y-2">
                @foreach ($users as $user)
                    <div class="flex items-center">
                        <input id="user_{{ $user->id }}" type="checkbox" wire:model="invited_users"
                            value="{{ $user->id }}"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="user_{{ $user->id }}" class="ml-2 text-sm font-medium text-white">
                            {{ $user->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('invited_users')" />
            <small class="text-gray-500">Select users to invite to this event.</small>
        </div>

        <div>
            <x-input-label for="visibility" :value="__('Requisition List Visibility')" />
            <select wire:model="visibility" id="visibility" name="visibility" class="mt-1 block w-full">
                <option value="0">Private</option>
                <option value="1">Public</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('visibility')" />
        </div>

        <div>
            <x-primary-button type="submit">Create Event</x-primary-button>
        </div>
    </form>
</div>
