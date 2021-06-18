<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications;

use Mediagone\Doctrine\Specifications\Specification;
use Mediagone\Doctrine\Specifications\SpecificationCompound;
use Mediagone\Doctrine\Specifications\SpecificationRepositoryResult;


final class FakeSpecificationCompound extends SpecificationCompound
{
    public static function create(Specification $specification)
    {
        return new self(SpecificationRepositoryResult::MANY_OBJECTS, $specification);
    }
    
    public static function createManyObjects()
    {
        return new self(SpecificationRepositoryResult::MANY_OBJECTS, new FakeEmptySpecification());
    }
    
    public static function createSingleObject()
    {
        return new self(SpecificationRepositoryResult::SINGLE_OBJECT, new FakeEmptySpecification());
    }
    
    public static function createSingleScalar()
    {
        return new self(SpecificationRepositoryResult::SINGLE_SCALAR, new FakeEmptySpecification());
    }
    
    public static function createInvalid()
    {
        return new self(-1, new FakeEmptySpecification());
    }
    
}
