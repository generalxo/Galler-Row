<?php

namespace App\Livewire\Storefront\Artworks;

use App\Enums\ArtworkType;
use App\Models\Artwork;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::store')]
#[Title('Artworks')]
class Index extends Component
{
    use WithPagination;

    /** ArtworkType value, or '' for every type. */
    #[Url]
    public string $type = '';

    /** 'originals', 'editions', or '' for both. */
    #[Url]
    public string $kind = '';

    #[Url]
    public bool $available = false;

    /** 'newest', 'price-asc' or 'price-desc'. */
    #[Url]
    public string $sort = 'newest';

    public function updated(): void
    {
        $this->resetPage();
    }

    public function paginationView(): string
    {
        return 'components.pagination';
    }

    public function render(): View
    {
        $type = ArtworkType::tryFrom($this->type);

        $artworks = Artwork::query()
            ->onDisplay()
            ->with(['images', 'artist'])
            ->when($type, fn ($query) => $query->ofType($type))
            ->when($this->kind === 'originals', fn ($query) => $query->originals())
            ->when($this->kind === 'editions', fn ($query) => $query->editions())
            ->when($this->available, fn ($query) => $query->available())
            ->when($this->sort === 'price-asc', fn ($query) => $query->orderBy('price'))
            ->when($this->sort === 'price-desc', fn ($query) => $query->orderByDesc('price'))
            ->when(! in_array($this->sort, ['price-asc', 'price-desc'], true), fn ($query) => $query->latest('published_at'))
            ->orderBy('id')
            ->paginate(12);

        return view('livewire.storefront.artworks.index', [
            'artworks' => $artworks,
            // Only offer types this store actually sells.
            'types' => array_values(array_filter(
                ArtworkType::cases(),
                fn (ArtworkType $case): bool => Artwork::query()->onDisplay()->ofType($case)->exists(),
            )),
        ]);
    }
}
