<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


final class FakeEmptySpecification implements Specification
{
    public function modifyBuilder(QueryBuilder $builder) : void
    {
    
    }
    
    public function modifyQuery(Query $query) : void
    {
    
    }
}
