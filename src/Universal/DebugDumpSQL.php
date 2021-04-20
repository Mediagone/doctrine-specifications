<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\Query;
use Mediagone\Doctrine\Specifications\Specification;


final class DebugDumpSQL extends Specification
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
    
    public function modifyQuery(Query $query) : void
    {
        dump($query->getSQL());
    }
    
    
    
}
