<?php declare(strict_types=1);

namespace Tests\Mediagone\DDD\Doctrine\Specifications;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mediagone\DDD\Doctrine\Specifications\Specification;


final class FakeEmptySpecification implements Specification
{
    public function modifyBuilder(QueryBuilder $builder) : void
    {
    
    }
    
    public function modifyQuery(Query $query) : void
    {
    
    }
}
