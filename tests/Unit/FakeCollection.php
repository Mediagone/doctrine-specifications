<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications;

use Mediagone\Doctrine\Specifications\Specification;
use Mediagone\Doctrine\Specifications\SpecificationCollection;
use Mediagone\Doctrine\Specifications\SpecificationRepositoryResult;


final class FakeCollection extends SpecificationCollection
{
    
    public static function entityFqcn() : string
    {
        return '';
    }
    
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
