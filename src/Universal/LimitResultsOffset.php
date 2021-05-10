<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\Query;
use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Specification;


class LimitResultsOffset extends Specification
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private int $offset;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private function __construct(int $offset)
    {
        if ($offset < 0) {
            throw new InvalidArgumentException('Offset must be zero or positive integer.');
        }
        
        $this->offset = $offset;
    }
    
    
    public static function specification(int $offset) : self
    {
        return new self($offset);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyQuery(Query $query) : void
    {
        if ($this->offset > 0) {
            $query->setFirstResult($this->offset);
        }
    }
    
    
    
}
