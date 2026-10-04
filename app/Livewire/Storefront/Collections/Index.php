<?php

namespace App\Livewire\Storefront\Collections;

use App\Models\Collection;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::store')]
#[Title('Collections')]
class Index extends Component
{
    public function render(): View
    {
        $collections = Collection::query()
            ->withCount(['artworks' => fn ($query) => $query->onDisplay()])
            ->with(['artworks' => fn ($query) => $query->onDisplay()->with('images')->orderByPivot('position')])
            ->orderBy('position')
            ->get();

        return view('livewire.storefront.collections.index', ['collections' => $collections]);
    }
}
