<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications;


final class SpecificationRepositoryResult
{
    public const MANY_OBJECTS = 0;
    public const SINGLE_OBJECT = 1;
    public const SINGLE_SCALAR = 2;
}
