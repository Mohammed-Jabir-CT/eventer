<?php

use App\Http\Controllers\ImageController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    Volt::route('events', 'events.index')->name('events.index');
    Volt::route('events/create', 'events.create')->name('events.create');
    Volt::route('events/{event}', 'events.view')->name('events.view');
    Volt::route('events/edit/{event}', 'events.edit')->name('events.edit');
    Volt::route('events/gallery/{event}', 'events.gallery')->name('events.gallery');
    Volt::route('requisition-items/create/{requisition_list}', 'req-items.create')->name('requisition-items.create');
});

require __DIR__ . '/auth.php';
