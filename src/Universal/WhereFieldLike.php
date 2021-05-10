<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class WhereFieldLike extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $aliasedField;
    
    private string $paramName;
    
    private string $value;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $aliasedField, string $paramName, string $value)
    {
        $this->aliasedField = $aliasedField;
        $this->paramName = $paramName;
        $this->value = $value;
    }
    
    
    public static function specification(string $aliasedField, string $paramName, string $value) : self
    {
        return new self($aliasedField, $paramName, $value);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder
            ->andWhere("$this->aliasedField LIKE :$this->paramName")
            ->setParameter($this->paramName, $this->value)
        ;
    }
    
    
    
}
