<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class Select extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $select;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $select)
    {
        $this->select = $select;
    }
    
    
    public static function specification(string $select) : self
    {
        return new self($select);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->addSelect($this->select);
    }
    
    
    
}
