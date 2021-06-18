<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Mediagone\Doctrine\Specifications\Specification;
use Mediagone\Doctrine\Specifications\SpecificationReadModel;


class SelectReadModel extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $className;
    
    private string $entityName;
    
    private string $entityAlias;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $entityName, string $entityAlias, string $className)
    {
        if (! is_a($className, SpecificationReadModel::class, true)) {
            throw new LogicException("The ReadModel class ($className) must implement the " . SpecificationReadModel::class . ' interface.');
        }
        
        $this->className = $className;
        $this->entityName = $entityName;
        $this->entityAlias = $entityAlias;
    }
    
    
    public static function specification(string $entityName, string $entityAlias, string $className) : self
    {
        return new self($entityName, $entityAlias, $className);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $fields = ($this->className)::getDqlConstructorArguments();
        
        $builder->from($this->entityName, $this->entityAlias);
        $builder->addSelect(new Func("NEW $this->className", $fields));
    }
    
    
    
}
