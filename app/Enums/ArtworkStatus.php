<?php

namespace App\Enums;

enum ArtworkStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Sold = 'sold';
    case Archived = 'archived';
}
