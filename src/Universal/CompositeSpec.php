<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class CompositeSpec extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private array $specifications;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(Specification... $specifications)
    {
        $this->specifications = $specifications;
    }
    
    
    public static function specification(Specification... $specifications) : self
    {
        return new self(...$specifications);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        foreach ($this->specifications as $spec) {
            $spec->modifyBuilder($builder);
        }
    }
    
    
    final public function modifyQuery(Query $query) : void
    {
        foreach ($this->specifications as $spec) {
            $spec->modifyQuery($query);
        }
    }
    
    
    
}
