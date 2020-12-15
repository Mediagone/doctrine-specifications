<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Mediagone\Doctrine\Specifications\Specification;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;


final class LimitResultsOffset implements Specification
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
    
    public function modifyBuilder(QueryBuilder $builder) : void
    {
        // Do nothing
    }
    
    
    public function modifyQuery(Query $query) : void
    {
        if ($this->offset > 0) {
            $query->setFirstResult($this->offset);
        }
    }
    
    
    
}
