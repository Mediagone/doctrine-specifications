<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;


abstract class Specification
{
    public function modifyBuilder(QueryBuilder $builder) : void
    {
        
    }
    
    public function modifyQuery(Query $query) : void
    {
    
    }
}
