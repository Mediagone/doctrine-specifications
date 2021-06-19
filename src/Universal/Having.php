<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class Having extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $having;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $having)
    {
        $this->having = $having;
    }
    
    
    public static function specification(string $having) : self
    {
        return new self($having);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->andHaving($this->having);
    }
    
    
    
}
