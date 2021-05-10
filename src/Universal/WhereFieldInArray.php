<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;
use function array_values;
use function implode;


class WhereFieldInArray extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $aliasedField;
    
    private string $paramName;
    
    private ?string $paramType;
    
    private array $values;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $aliasedField, string $paramName, array $values, ?string $paramType)
    {
        $this->aliasedField = $aliasedField;
        $this->paramName = $paramName;
        $this->paramType = $paramType;
        $this->values = $values;
    }
    
    
    public static function specification(string $aliasedField, string $paramName, array $values, ?string $paramType = null) : self
    {
        return new self($aliasedField, $paramName, $values, $paramType);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $params = [];
        $placeholders = [];
        
        foreach (array_values($this->values) as $index => $val) {
            $params[$this->paramName . $index] = $val;
            $placeholders[] = ':' . $this->paramName . $index;
        }
        
        $builder->andWhere("$this->aliasedField IN (" . implode(',', $placeholders) . ")");
        
        foreach ($params as $paramName => $paramValue) {
            $builder->setParameter($paramName, $paramValue, $this->paramType);
        }
    }
    
    
    
}
