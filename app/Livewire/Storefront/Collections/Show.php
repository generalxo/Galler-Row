<?php

namespace App\Livewire\Storefront\Collections;

use App\Models\Collection;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::store')]
class Show extends Component
{
    public Collection $collection;

    public function render(): View
    {
        return view('livewire.storefront.collections.show', [
            'artworks' => $this->collection->artworks()
                ->onDisplay()
                ->with(['images', 'artist'])
                ->orderByPivot('position')
                ->get(),
        ])->title($this->collection->name);
    }
}
