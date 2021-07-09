<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class WhereClause extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $whereClause;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $whereClause)
    {
        $this->whereClause = $whereClause;
    }
    
    
    public static function specification(string $whereClause) : self
    {
        return new self($whereClause);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->andWhere($this->whereClause);
    }
    
    
    
}
