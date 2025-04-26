<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Event;

new class extends Component {
    use WithPagination;
    public function render(): mixed
    {
        return view('livewire.events.index', [
            'events' => Event::orderBy('created_at', 'desc')
                ->with(['createdBy', 'eventFor', 'users'])
                ->paginate(10),
        ]);
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Events') }}
            </h2>
            <a href="{{ route('events.create') }}">
                <x-primary-button>
                    {{ __('Create Event') }}
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 bg-white rounded-lg shadow-md">
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created By
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($events as $event)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $event->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $event->date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $event->time }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $event->createdBy ? $event->createdBy->name : 'N/A' }}
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        {{-- @can('update', $event)
                                            <a href="{{ route('events.edit', $event->id) }}">
                                                <x-secondary-button>
                                                    {{ __('Edit') }}
                                                </x-secondary-button>
                                            </a>
                                        @endcan --}}
                                        <a href="{{ route('events.view', $event->id) }}">
                                            <x-primary-button>
                                                {{ __('View') }}
                                            </x-primary-button>
                                        </a>
                                        {{-- @can('delete', $event)
                                            <a href="{{ route('events.view', $event->id) }}">
                                                <x-danger-button>
                                                    {{ __('Delete') }}
                                                </x-danger-button>
                                            </a>
                                        @endcan --}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $events->links() }}
            </div>
        </div>
    </div>
</div>
