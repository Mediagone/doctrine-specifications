<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications;

use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\SpecificationRepositoryResult;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpDQL;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpSQL;
use PHPUnit\Framework\TestCase;


/**
 * @covers \Mediagone\Doctrine\Specifications\SpecificationCompound
 */
final class SpecificationCompoundTest extends TestCase
{
    public function test_can_use_many_objects_result(): void
    {
        $specificationSet = FakeSpecificationCompound::createManyObjects();
        
        self::assertCount(1, $specificationSet->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $specificationSet->getSpecifications()[0]);
        self::assertSame(SpecificationRepositoryResult::MANY_OBJECTS, $specificationSet->getRepositoryResult());
    }
    
    public function test_can_use_single_object_result(): void
    {
        $specificationSet = FakeSpecificationCompound::createSingleObject();
        
        self::assertCount(1, $specificationSet->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $specificationSet->getSpecifications()[0]);
        self::assertSame(SpecificationRepositoryResult::SINGLE_OBJECT, $specificationSet->getRepositoryResult());
    }
    
    public function test_can_use_single_scalar_result(): void
    {
        $specificationSet = FakeSpecificationCompound::createSingleScalar();
        
        self::assertCount(1, $specificationSet->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $specificationSet->getSpecifications()[0]);
        self::assertSame(SpecificationRepositoryResult::SINGLE_SCALAR, $specificationSet->getRepositoryResult());
    }
    
    public function test_cannot_use_invalid_result(): void
    {
        $this->expectException(InvalidArgumentException::class);
        FakeSpecificationCompound::createInvalid();
    }
    
    public function test_can_use_dumpDQL_specification(): void
    {
        $specificationSet = FakeSpecificationCompound::createManyObjects()->dumpDQL();
        
        self::assertCount(2, $specificationSet->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $specificationSet->getSpecifications()[0]);
        self::assertInstanceOf(DebugDumpDQL::class, $specificationSet->getSpecifications()[1]);
    }
    
    public function test_can_use_dumpSQL_specification(): void
    {
        $specificationSet = FakeSpecificationCompound::createManyObjects()->dumpSQL();
        
        self::assertCount(2, $specificationSet->getSpecifications());
        self::assertInstanceOf(FakeEmptySpecification::class, $specificationSet->getSpecifications()[0]);
        self::assertInstanceOf(DebugDumpSQL::class, $specificationSet->getSpecifications()[1]);
    }
    
    
}
