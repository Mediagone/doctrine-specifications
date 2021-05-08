<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


abstract class WhereField extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $aliasedField;
    
    private string $paramName;
    
    private string $paramType;
    
    private string $operator;
    
    /**
     * @var mixed $value
     */
    private $value;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $aliasedField, string $paramName, string $operator, $value, string $paramType)
    {
        $this->aliasedField = $aliasedField;
        $this->paramName = $paramName;
        $this->paramType = $paramType;
        $this->operator = $operator;
        $this->value = $value;
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder
            ->andWhere("$this->aliasedField $this->operator :$this->paramName")
            ->setParameter($this->paramName, $this->value, $this->paramType)
        ;
    }
    
    
    
}
