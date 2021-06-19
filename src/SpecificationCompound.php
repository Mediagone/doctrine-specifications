<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpDQL;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpSQL;
use Mediagone\Doctrine\Specifications\Universal\Having;
use Mediagone\Doctrine\Specifications\Universal\JoinInner;
use Mediagone\Doctrine\Specifications\Universal\JoinLeft;
use Mediagone\Doctrine\Specifications\Universal\LimitResultsMaxCount;
use Mediagone\Doctrine\Specifications\Universal\LimitResultsOffset;
use Mediagone\Doctrine\Specifications\Universal\LimitResultsPaginate;
use Mediagone\Doctrine\Specifications\Universal\ModifyBuilder;
use Mediagone\Doctrine\Specifications\Universal\ModifyQuery;
use Mediagone\Doctrine\Specifications\Universal\OrderResultsByAsc;
use Mediagone\Doctrine\Specifications\Universal\OrderResultsByDesc;
use Mediagone\Doctrine\Specifications\Universal\Select;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldBetween;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldBetweenExclusive;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldDifferent;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldEqual;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldGreater;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldGreaterOrEqual;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldIn;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldInArray;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldIsNotNull;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldIsNull;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldLesser;
use Mediagone\Doctrine\Specifications\Universal\WhereFieldLesserOrEqual;
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
    
    final protected function __construct(int $repositoryResult, Specification... $specifications)
    {
        if (! in_array($repositoryResult, [
            SpecificationRepositoryResult::MANY_OBJECTS,
            SpecificationRepositoryResult::SINGLE_OBJECT,
            SpecificationRepositoryResult::SINGLE_SCALAR
        ])) {
            throw new InvalidArgumentException('Invalid SpecificationRepositoryResult value, use one of the following constants: MANY_OBJECTS, SINGLE_OBJECT or SINGLE_SCALAR.');
        }
    
        $this->repositoryResult = $repositoryResult;
        foreach ($specifications as $spec) {
            $this->specifications[] = $spec;
        }
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
    
    final protected function select(string $select) : void
    {
        $this->addSpecification(Select::specification($select));
    }
    
    final protected function having(string $having) : void
    {
        $this->addSpecification(Having::specification($having));
    }
    
    final protected function joinLeft(string $join, string $alias, ?string $condition = null, ?string $indexBy = null) : void
    {
        $this->addSpecification(JoinLeft::specification($join, $alias, $condition, $indexBy));
    }
    
    final protected function joinInner(string $join, string $alias, ?string $condition = null, ?string $indexBy = null) : void
    {
        $this->addSpecification(JoinInner::specification($join, $alias, $condition, $indexBy));
    }
    
    
    
    final protected function whereFieldEqual(string $aliasedField, string $paramName, $value, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldEqual::specification($aliasedField, $paramName, $value, $paramType));
    }
    
    final protected function whereFieldDifferent(string $aliasedField, string $paramName, $value, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldDifferent::specification($aliasedField, $paramName, $value, $paramType));
    }
    
    
    
    final protected function whereFieldGreater(string $aliasedField, string $paramName, $value, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldGreater::specification($aliasedField, $paramName, $value, $paramType));
    }
    
    final protected function whereFieldGreaterOrEqual(string $aliasedField, string $paramName, $value, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldGreaterOrEqual::specification($aliasedField, $paramName, $value, $paramType));
    }
    
    
    
    final protected function whereFieldLesser(string $aliasedField, string $paramName, $value, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldLesser::specification($aliasedField, $paramName, $value, $paramType));
    }
    
    final protected function whereFieldLesserOrEqual(string $aliasedField, string $paramName, $value, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldLesserOrEqual::specification($aliasedField, $paramName, $value, $paramType));
    }
    
    
    
    final protected function whereFieldIn(string $aliasedField, string $value) : void
    {
        $this->addSpecification(WhereFieldIn::specification($aliasedField, $value));
    }
    
    final protected function whereFieldInArray(string $aliasedField, string $paramName, array $values, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldInArray::specification($aliasedField, $paramName, $values, $paramType));
    }
    
    
    
    final protected function whereFieldIsNull(string $aliasedField) : void
    {
        $this->addSpecification(WhereFieldIsNull::specification($aliasedField));
    }
    
    final protected function whereFieldIsNotNull(string $aliasedField) : void
    {
        $this->addSpecification(WhereFieldIsNotNull::specification($aliasedField));
    }
    
    
    
    final protected function whereFieldLike(string $aliasedField, string $paramName, string $value) : void
    {
        $this->addSpecification(WhereFieldLike::specification($aliasedField, $paramName, $value));
    }
    
    
    
    final protected function whereFieldBetween(string $aliasedField, string $paramName, $valueMin, $valueMax, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldBetween::specification($aliasedField, $paramName, $valueMin, $valueMax, $paramType));
    }
    
    final protected function whereFieldBetweenExclusive(string $aliasedField, string $paramName, $valueMin, $valueMax, ?string $paramType = null) : void
    {
        $this->addSpecification(WhereFieldBetweenExclusive::specification($aliasedField, $paramName, $valueMin, $valueMax, $paramType));
    }
    
    
    
    final protected function orderResultsByAsc(string $expression) : void
    {
        $this->addSpecification(OrderResultsByAsc::specification($expression));
    }
    
    final protected function orderResultsByDesc(string $expression) : void
    {
        $this->addSpecification(OrderResultsByDesc::specification($expression));
    }
    
    
    
    
    final protected function modifyBuilder(callable $callback) : void
    {
        $this->addSpecification(ModifyBuilder::specification($callback));
    }
    
    final protected function modifyQuery(callable $callback) : void
    {
        $this->addSpecification(ModifyQuery::specification($callback));
    }
    
    
    
    final protected function limitResultsOffset(int $offset) : void
    {
        $this->addSpecification(LimitResultsOffset::specification($offset));
    }
    
    final protected function limitResultsMaxCount(int $count) : void
    {
        $this->addSpecification(LimitResultsMaxCount::specification($count));
    }
    
    final protected function limitResultsPaginate(int $pageNumber, int $itemsPerPage) : void
    {
        $this->addSpecification(LimitResultsPaginate::specification($pageNumber, $itemsPerPage));
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
