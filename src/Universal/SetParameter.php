<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class SetParameter extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $paramName;
    
    private ?string $paramType;
    
    /**
     * @var mixed $value
     */
    private $value;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $paramName, $value, ?string $paramType = null)
    {
        $this->paramName = $paramName;
        $this->paramType = $paramType;
        $this->value = $value;
    }
    
    
    public static function specification(string $paramName, $value, ?string $paramType = null) : self
    {
        return new self($paramName, $value, $paramType);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->setParameter($this->paramName, $this->value, $this->paramType);
    }
    
    
    
}
