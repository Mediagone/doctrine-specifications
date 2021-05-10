<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\Query;
use Mediagone\Doctrine\Specifications\Specification;


class DebugDumpSQL extends Specification
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
    
    final public function modifyQuery(Query $query) : void
    {
        /** @noinspection ForgottenDebugOutputInspection */
        dump($query->getSQL());
    }
    
    
    
}
