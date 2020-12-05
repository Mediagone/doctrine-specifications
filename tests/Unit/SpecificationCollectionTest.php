<?php declare(strict_types=1);

namespace Tests\Mediagone\DDD\Doctrine\Specifications;

use InvalidArgumentException;
use Mediagone\DDD\Doctrine\Specifications\SpecificationRepositoryResult;
use Mediagone\DDD\Doctrine\Specifications\Universal\DebugDumpDQL;
use Mediagone\DDD\Doctrine\Specifications\Universal\DebugDumpSQL;
use PHPUnit\Framework\TestCase;


final class SpecificationCollectionTest extends TestCase
{
    public function test_can_use_many_objects_result(): void
    {
        $collection = FakeCollection::createManyObjects();
        
        self::assertCount(1, $collection->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $collection->getSpecifications()[0]);
        self::assertSame(SpecificationRepositoryResult::MANY_OBJECTS, $collection->getRepositoryResult());
    }
    
    public function test_can_use_single_object_result(): void
    {
        $collection = FakeCollection::createSingleObject();
        
        self::assertCount(1, $collection->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $collection->getSpecifications()[0]);
        self::assertSame(SpecificationRepositoryResult::SINGLE_OBJECT, $collection->getRepositoryResult());
    }
    
    public function test_can_use_single_scalar_result(): void
    {
        $collection = FakeCollection::createSingleScalar();
        
        self::assertCount(1, $collection->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $collection->getSpecifications()[0]);
        self::assertSame(SpecificationRepositoryResult::SINGLE_SCALAR, $collection->getRepositoryResult());
    }
    
    public function test_cannot_use_invalid_result(): void
    {
        $this->expectException(InvalidArgumentException::class);
        FakeCollection::createInvalid();
    }
    
    public function test_can_use_dumpDQL_specification(): void
    {
        $collection = FakeCollection::createManyObjects()->dumpDQL();
        
        self::assertCount(2, $collection->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $collection->getSpecifications()[0]);
        self::assertInstanceOf(DebugDumpDQL::class, $collection->getSpecifications()[1]);
    }
    
    public function test_can_use_dumpSQL_specification(): void
    {
        $collection = FakeCollection::createManyObjects()->dumpSQL();
        
        self::assertCount(2, $collection->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $collection->getSpecifications()[0]);
        self::assertInstanceOf(DebugDumpSQL::class, $collection->getSpecifications()[1]);
    }
    
    
}
