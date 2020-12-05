<?php declare(strict_types=1);

namespace Tests\Mediagone\DDD\Doctrine\Specifications;

use Mediagone\DDD\Doctrine\Specifications\Specification;
use Mediagone\DDD\Doctrine\Specifications\SpecificationCollection;
use Mediagone\DDD\Doctrine\Specifications\SpecificationRepositoryResult;


final class FakeCollection extends SpecificationCollection
{
    
    public static function create(Specification $specification)
    {
        return new self($specification, SpecificationRepositoryResult::MANY_OBJECTS);
    }
    
    public static function createManyObjects()
    {
        return new self(new FakeEmptySpecification(), SpecificationRepositoryResult::MANY_OBJECTS);
    }
    
    public static function createSingleObject()
    {
        return new self(new FakeEmptySpecification(), SpecificationRepositoryResult::SINGLE_OBJECT);
    }
    
    public static function createSingleScalar()
    {
        return new self(new FakeEmptySpecification(), SpecificationRepositoryResult::SINGLE_SCALAR);
    }
    
    public static function createInvalid()
    {
        return new self(new FakeEmptySpecification(), -1);
    }
    
}
