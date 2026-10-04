<?php

namespace App\Livewire;

use App\Enums\StoreStatus;
use App\Models\Store;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Open your own art store')]
class Home extends Component
{
    public function render(): View
    {
        return view('livewire.home', [
            'stores' => Store::query()
                ->where('status', StoreStatus::Active)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
