<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Universal\CallbackQuery;
use Mediagone\Doctrine\Specifications\Universal\ModifyBuilder;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpDQL;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpSQL;
use Mediagone\Doctrine\Specifications\Universal\OrderResultsByAsc;
use Mediagone\Doctrine\Specifications\Universal\OrderResultsByDesc;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldBetween;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldBetweenExclusive;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldDifferentFrom;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldEqualTo;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldGreaterThan;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldGreaterThanOrEqualTo;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldIn;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldInArray;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldIsNotNull;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldIsNull;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldLesserThan;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldLesserThanOrEqualTo;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldLike;
use function in_array;


abstract class SpecificationCompound
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
    
    public function getEntityManager(ManagerRegistry $registry) : EntityManager
    {
        return $registry->getManager();
    }
    
    
    
    //========================================================================================================
    // Generic specifications
    //========================================================================================================
    
    final protected function whereFieldEqualTo(string $aliasedField, string $paramName, $value, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldEqualTo::specification($aliasedField, $paramName, $value, $paramType));
        return $this;
    }
    
    final protected function whereFieldDifferentFrom(string $aliasedField, string $paramName, $value, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldDifferentFrom::specification($aliasedField, $paramName, $value, $paramType));
        return $this;
    }
    
    
    
    final protected function whereFieldGreaterThan(string $aliasedField, string $paramName, $value, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldGreaterThan::specification($aliasedField, $paramName, $value, $paramType));
        return $this;
    }
    
    final protected function whereFieldGreaterThanOrEqual(string $aliasedField, string $paramName, $value, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldGreaterThanOrEqualTo::specification($aliasedField, $paramName, $value, $paramType));
        return $this;
    }
    
    
    
    final protected function whereFieldLesserThan(string $aliasedField, string $paramName, $value, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldLesserThan::specification($aliasedField, $paramName, $value, $paramType));
        return $this;
    }
    
    final protected function whereFieldLesserThanOrEqualTo(string $aliasedField, string $paramName, $value, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldLesserThanOrEqualTo::specification($aliasedField, $paramName, $value, $paramType));
        return $this;
    }
    
    
    
    final protected function whereFieldIn(string $aliasedField, string $value) : self
    {
        $this->addSpecification(WhereFieldIn::specification($aliasedField, $value));
        return $this;
    }
    
    final protected function whereFieldInArray(string $aliasedField, string $paramName, array $values, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldInArray::specification($aliasedField, $paramName, $values, $paramType));
        return $this;
    }
    
    
    
    final protected function whereFieldIsNull(string $aliasedField) : self
    {
        $this->addSpecification(WhereFieldIsNull::specification($aliasedField));
        return $this;
    }
    
    final protected function whereFieldIsNotNull(string $aliasedField) : self
    {
        $this->addSpecification(WhereFieldIsNotNull::specification($aliasedField));
        return $this;
    }
    
    
    
    final protected function whereFieldLike(string $aliasedField, string $paramName, string $value) : self
    {
        $this->addSpecification(WhereFieldLike::specification($aliasedField, $paramName, $value));
        return $this;
    }
    
    
    
    final protected function whereFieldBetween(string $aliasedField, string $paramName, $valueMin, $valueMax, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldBetween::specification($aliasedField, $paramName, $valueMin, $valueMax, $paramType));
        return $this;
    }
    
    final protected function whereFieldBetweenExclusive(string $aliasedField, string $paramName, $valueMin, $valueMax, ?string $paramType = null) : self
    {
        $this->addSpecification(WhereFieldBetweenExclusive::specification($aliasedField, $paramName, $valueMin, $valueMax, $paramType));
        return $this;
    }
    
    
    
    final protected function orderResultsByAsc(string $expression) : self
    {
        $this->addSpecification(OrderResultsByAsc::specification($expression));
        return $this;
    }
    
    final protected function orderResultsByDesc(string $expression) : self
    {
        $this->addSpecification(OrderResultsByDesc::specification($expression));
        return $this;
    }
    
    
    
    
    final protected function modifyBuilder(callable $callback) : self
    {
        $this->addSpecification(ModifyBuilder::specification($callback));
        return $this;
    }
    
    final protected function callbackQuery(callable $callback) : self
    {
        $this->addSpecification(CallbackQuery::specification($callback));
        return $this;
    }


    
    //========================================================================================================
    // Generic specifications
    //========================================================================================================
    
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
