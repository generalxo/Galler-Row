<?php

namespace App\Exceptions;

use InvalidArgumentException;

class InvalidArtworkInventory extends InvalidArgumentException
{
    public static function originalWithStock(int $quantity): self
    {
        return new self("An original artwork can have at most 1 in stock, {$quantity} given.");
    }

    public static function originalWithEditionSize(): self
    {
        return new self('An original artwork cannot have an edition size.');
    }

    public static function editionWithoutSize(): self
    {
        return new self('An edition must have an edition size.');
    }

    public static function stockExceedsEdition(int $quantity, int $editionSize): self
    {
        return new self("Stock ({$quantity}) cannot exceed the edition size ({$editionSize}).");
    }
}
