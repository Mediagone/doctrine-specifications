<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Mediagone\Doctrine\Specifications\Specification;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;


final class LimitResultsMaxCount implements Specification
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
    
    public function modifyBuilder(QueryBuilder $builder) : void
    {
        // Do nothing
    }
    
    
    public function modifyQuery(Query $query) : void
    {
        $query->setMaxResults($this->count);
    }
    
    
    
}
