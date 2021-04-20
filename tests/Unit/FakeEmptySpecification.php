<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


final class FakeEmptySpecification extends Specification
{
    public function modifyBuilder(QueryBuilder $builder) : void
    {
        // Useless, but explicit
    }
    
    public function modifyQuery(Query $query) : void
    {
        // Useless, but explicit
    }
}
