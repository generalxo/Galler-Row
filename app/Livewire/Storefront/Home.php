<?php

namespace App\Livewire\Storefront;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Collection;
use App\Support\CurrentStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::store')]
class Home extends Component
{
    public function render(CurrentStore $currentStore): View
    {
        $featured = Collection::query()
            ->where('is_featured', true)
            ->orderBy('position')
            ->first();

        return view('livewire.storefront.home', [
            'featured' => $featured,
            'featuredArtworks' => $featured?->artworks()
                ->onDisplay()
                ->with(['images', 'artist'])
                ->orderByPivot('position')
                ->limit(4)
                ->get() ?? collect(),
            'latest' => Artwork::query()
                ->onDisplay()
                ->with(['images', 'artist'])
                ->latest('published_at')
                ->limit(8)
                ->get(),
            'artists' => Artist::query()
                ->whereHas('artworks', fn ($query) => $query->onDisplay())
                ->orderBy('name')
                ->get(),
        ])->title($currentStore->get()->name ?? '');
    }
}
