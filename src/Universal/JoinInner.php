<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class JoinInner extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $join;
    
    private string $alias;
    
    private ?string $condition;
    
    private ?string $indexBy;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $join, string $alias, ?string $condition, ?string $indexBy)
    {
        $this->join = $join;
        $this->alias = $alias;
        $this->condition = $condition;
        $this->indexBy = $indexBy;
    }
    
    
    public static function specification(string $join, string $alias, ?string $condition = null, ?string $indexBy = null) : self
    {
        return new self($join, $alias, $condition, $indexBy);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $conditionType = $this->condition !== null ? 'WITH' : null;
        
        $builder->innerJoin($this->join, $this->alias, $conditionType, $this->condition, $this->indexBy);
    }
    
    
    
}
