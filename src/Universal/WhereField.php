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
    
    private string $operator;
    
    /**
     * @var mixed $value
     */
    private $value;
    
    private string $type;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $aliasedField, string $paramName, string $operator, $value, string $type)
    {
        $this->aliasedField = $aliasedField;
        $this->paramName = $paramName;
        $this->operator = $operator;
        $this->value = $value;
        $this->type = $type;
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder
            ->andWhere("$this->aliasedField $this->operator :$this->paramName")
            ->setParameter($this->paramName, $this->value, $this->type)
        ;
    }
    
    
    
}
