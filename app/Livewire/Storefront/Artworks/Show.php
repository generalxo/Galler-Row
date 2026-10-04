<?php

namespace App\Livewire\Storefront\Artworks;

use App\Enums\ArtworkStatus;
use App\Models\Artwork;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::store')]
class Show extends Component
{
    public Artwork $artwork;

    public function mount(Artwork $artwork): void
    {
        abort_unless(in_array($artwork->status, [ArtworkStatus::Published, ArtworkStatus::Sold], true), 404);

        $this->artwork = $artwork->load(['images', 'artist', 'artworkable', 'variants' => fn ($query) => $query->orderBy('position'), 'collections']);
    }

    public function render(): View
    {
        return view('livewire.storefront.artworks.show')
            ->title($this->artwork->title);
    }
}
