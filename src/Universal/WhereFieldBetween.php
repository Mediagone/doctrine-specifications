<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


final class WhereFieldBetween extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $aliasedField;
    
    private string $paramName;
    
    private ?string $paramType;
    
    /**
     * @var mixed $valueMin
     */
    private $valueMin;
    
    /**
     * @var mixed $valueMax
     */
    private $valueMax;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $aliasedField, string $paramName, $valueMin, $valueMax, ?string $paramType)
    {
        $this->aliasedField = $aliasedField;
        $this->paramName = $paramName;
        $this->paramType = $paramType;
        $this->valueMin = $valueMin;
        $this->valueMax = $valueMax;
    }
    
    
    public static function specification(string $aliasedField, string $paramName, $valueMin, $valueMax, ?string $paramType = null) : self
    {
        return new self($aliasedField, $paramName, $valueMin, $valueMax, $paramType);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $paramMin = $this->paramName.'Min';
        $paramMax = $this->paramName.'Max';
        
        $builder
            ->andWhere("$this->aliasedField >= :$paramMin AND $this->aliasedField <= :$paramMax")
            ->setParameter($paramMin, $this->valueMin, $this->paramType)
            ->setParameter($paramMax, $this->valueMax, $this->paramType)
        ;
    }
    
    
    
}
