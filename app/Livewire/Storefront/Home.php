<?php

namespace App\Livewire\Storefront;

use App\Models\Store;
use App\Support\CurrentStore;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Home extends Component
{
    public function render(CurrentStore $currentStore): View
    {
        /** @var Store $store */
        $store = $currentStore->get();

        return view('livewire.storefront.home', ['store' => $store])
            ->title($store->name);
    }
}
