<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class ModifyBuilder extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private $callback;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
    
    
    public static function specification(callable $callback) : self
    {
        return new self($callback);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        ($this->callback)($builder);
    }
    
    
    
}
