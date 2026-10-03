<?php

namespace App\Models\Concerns;

use App\Models\Artwork;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Shared behaviour for the type-specific detail models behind an artwork.
 */
trait IsArtworkDetail
{
    /**
     * @return MorphOne<Artwork, $this>
     */
    public function artwork(): MorphOne
    {
        return $this->morphOne(Artwork::class, 'artworkable');
    }
}
