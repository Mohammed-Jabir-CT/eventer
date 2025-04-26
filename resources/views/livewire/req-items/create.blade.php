<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\RequisitionItem;
use App\Models\RequisitionList;

new class extends Component {
    #[Validate('required|exists:requisition_lists,id')]
    public ?int $requisition_list_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('boolean')]
    public bool $is_optional = false;

    public function mount(RequisitionList $requisition_list)
    {
        $requisition_list_id = $requisition_list->id;
        $this->authorize('create', $requisition_list->event);
        $this->requisition_list_id = $requisition_list_id;
    }

    public function save()
    {
        $this->validate();

        RequisitionItem::create([
            'requisition_list_id' => $this->requisition_list_id,
            'name' => $this->name,
            'availability' => true,
            'is_optional' => $this->is_optional,
            'claimed_by' => null,
        ]);

        session()->flash('success', 'Requisition item added successfully!');
        $this->reset('name', 'is_optional');
    }
};
?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Requisition Items') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-10">
        @if (session()->has('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6">
            <input type="hidden" wire:model="requisition_list_id" />

            <div>
                <x-input-label for="name" :value="__('Item Name')" />
                <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full"
                    required autofocus autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="is_optional"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-600">Optional</span>
                </label>
            </div>

            <div>
                <x-primary-button type="submit">Add Item</x-primary-button>
            </div>
        </form>
    </div>
</div>
