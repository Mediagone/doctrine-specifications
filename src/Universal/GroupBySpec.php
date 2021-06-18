<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class GroupBySpec extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $groupBy;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $groupBy)
    {
        $this->groupBy = $groupBy;
    }
    
    
    public static function specification(string $groupBy) : self
    {
        return new self($groupBy);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->addGroupBy($this->groupBy);
    }
    
    
    
}
