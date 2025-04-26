<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\EventPhoto;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithFileUploads;

    public $event;
    public $photos = [];
    public $newPhoto;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->photos = $event->photos()->latest()->get();
    }

    public function addPhoto()
    {
        $this->authorize('create', [EventPhoto::class, $this->event]);
        $this->validate([
            'newPhoto' => 'image|max:1024',
        ]);

        // Store the photo in a private location
        $path = $this->newPhoto->store('event_photos', 'public');

        EventPhoto::create([
            'event_id' => $this->event->id,
            'uploaded_by' => Auth::id(),
            'file_path' => $path,
        ]);

        $this->photos = $this->event->photos()->latest()->get();
        $this->newPhoto = null;
    }
};
?>
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gallery') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('create', [App\Models\EventPhoto::class, $event])
                        <form wire:submit.prevent="addPhoto">
                            <input type="file" wire:model="newPhoto">
                            @error('newPhoto')
                                <span class="error">{{ $message }}</span>
                            @enderror
                            <x-primary-button type="submit">Add Photo</x-primary-button>
                        </form>
                    @endcan

                    <div>
                        @foreach ($photos as $photo)
                            <img src="{{ asset('storage/' . $photo->file_path) }}" alt="Event Photo">
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
