<?php declare(strict_types = 1);

namespace Lettland\PackageRecipe\Filter;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface Filter
{
    public static function getName(): string;

    public function process(mixed $value, mixed $originalValue = null): mixed;
}
