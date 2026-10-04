<?php

namespace App\Livewire\Storefront\Artists;

use App\Models\Artist;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::store')]
class Show extends Component
{
    public Artist $artist;

    public function render(): View
    {
        return view('livewire.storefront.artists.show', [
            'artworks' => $this->artist->artworks()
                ->onDisplay()
                ->with(['images', 'artist'])
                ->orderByDesc('year')
                ->get(),
        ])->title($this->artist->name);
    }
}
