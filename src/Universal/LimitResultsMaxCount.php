<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\Query;
use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Specification;


class LimitResultsMaxCount extends Specification
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private int $count;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private function __construct(int $count)
    {
        if ($count <= 0) {
            throw new InvalidArgumentException('Count must be a positive integer.');
        }
        
        $this->count = $count;
    }
    
    
    public static function specification(int $count) : self
    {
        return new self($count);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyQuery(Query $query) : void
    {
        $query->setMaxResults($this->count);
    }
    
    
    
}
