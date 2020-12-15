<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications;

use Mediagone\Doctrine\Specifications\SpecificationRepositoryResult;
use PHPUnit\Framework\TestCase;


final class SpecificationRepositoryResultTest extends TestCase
{
    public function test_many_objects_value_is_valid(): void
    {
        self::assertSame(0, SpecificationRepositoryResult::MANY_OBJECTS);
    }
    
    public function test_single_object_value_is_valid(): void
    {
        self::assertSame(1, SpecificationRepositoryResult::SINGLE_OBJECT);
    }
    
    public function test_single_scalar_value_is_valid(): void
    {
        self::assertSame(2, SpecificationRepositoryResult::SINGLE_SCALAR);
    }
    
    
}
