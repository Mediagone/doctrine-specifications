<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class OrderResultsByDesc extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $expression;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $expression)
    {
        $this->expression = $expression;
    }
    
    
    public static function specification(string $expression) : self
    {
        return new self($expression);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->addOrderBy($this->expression, 'DESC');
    }
    
    
    
}
