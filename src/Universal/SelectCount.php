<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class SelectCount extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $entityName;
    
    private string $entityAlias;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $entityName, string $entityAlias)
    {
        $this->entityName = $entityName;
        $this->entityAlias = $entityAlias;
    }
    
    
    public static function specification(string $entityName, string $entityAlias) : self
    {
        return new self($entityName, $entityAlias);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->from($this->entityName, $this->entityAlias);
        $builder->addSelect("COUNT($this->entityAlias)");
    }
    
    
    
}
