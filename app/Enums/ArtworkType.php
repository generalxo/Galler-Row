<?php

namespace App\Enums;

use App\Models\DigitalDetail;
use App\Models\PaintingDetail;
use App\Models\PhotographDetail;
use App\Models\PrintDetail;
use App\Models\SculptureDetail;
use Illuminate\Database\Eloquent\Model;

/**
 * Artwork types. Each value doubles as the morph map key of its detail model.
 */
enum ArtworkType: string
{
    case Painting = 'painting';
    case Print = 'print';
    case Photograph = 'photograph';
    case Sculpture = 'sculpture';
    case Digital = 'digital';

    /**
     * The detail model class that holds the type-specific columns.
     *
     * @return class-string<Model>
     */
    public function detailModel(): string
    {
        return match ($this) {
            self::Painting => PaintingDetail::class,
            self::Print => PrintDetail::class,
            self::Photograph => PhotographDetail::class,
            self::Sculpture => SculptureDetail::class,
            self::Digital => DigitalDetail::class,
        };
    }

    /**
     * Morph map entries for every detail model.
     *
     * @return array<string, class-string<Model>>
     */
    public static function morphMap(): array
    {
        $map = [];

        foreach (self::cases() as $type) {
            $map[$type->value] = $type->detailModel();
        }

        return $map;
    }
}
