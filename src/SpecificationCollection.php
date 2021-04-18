<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications;

use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpDQL;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpSQL;
use function in_array;


abstract class SpecificationCollection
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    /** @var Specification[] $specifications */
    private array $specifications = [];
    
    final protected function addSpecification(Specification $specification) : void
    {
        $this->specifications[] = $specification;
    }
    
    /**
     * @return Specification[]
     */
    final public function getSpecifications() : array
    {
        return $this->specifications;
    }
    
    
    private int $repositoryResult;
    
    final public function getRepositoryResult() : int
    {
        return $this->repositoryResult;
    }
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    final protected function __construct(Specification $specification, int $repositoryResult)
    {
        if (! in_array($repositoryResult, [
            SpecificationRepositoryResult::MANY_OBJECTS,
            SpecificationRepositoryResult::SINGLE_OBJECT,
            SpecificationRepositoryResult::SINGLE_SCALAR
        ])) {
            throw new InvalidArgumentException('Invalid SpecificationRepositoryResult value, use one of the following constants: MANY_OBJECTS, SINGLE_OBJECT or SINGLE_SCALAR.');
        }
        
        $this->specifications[] = $specification;
        $this->repositoryResult = $repositoryResult;
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    abstract public static function entityFqcn() : string;
    
    
    final public function dumpDQL() : self
    {
        $this->addSpecification(DebugDumpDQL::specification());
        return $this;
    }
    
    
    final public function dumpSQL() : self
    {
        $this->addSpecification(DebugDumpSQL::specification());
        return $this;
    }
    
    
    
}
