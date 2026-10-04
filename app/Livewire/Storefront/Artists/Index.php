<?php

namespace App\Livewire\Storefront\Artists;

use App\Models\Artist;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::store')]
#[Title('Artists')]
class Index extends Component
{
    public function render(): View
    {
        $artists = Artist::query()
            ->whereHas('artworks', fn ($query) => $query->onDisplay())
            ->withCount(['artworks' => fn ($query) => $query->onDisplay()])
            ->orderBy('name')
            ->get();

        return view('livewire.storefront.artists.index', ['artists' => $artists]);
    }
}
