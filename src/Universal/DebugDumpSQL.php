<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Mediagone\Doctrine\Specifications\Specification;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;


final class DebugDumpSQL implements Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private function __construct()
    {
    
    }
    
    
    public static function specification() : self
    {
        return new self();
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
        dump($query->getSQL());
    }
    
    
    
}
