<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications;


interface SpecificationReadModel
{
    public static function getDqlConstructorArguments() : array;
}
