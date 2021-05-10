<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


class WhereFieldIn extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $aliasedField;
    
    private string $value;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $aliasedField, string $value)
    {
        $this->aliasedField = $aliasedField;
        $this->value = $value;
    }
    
    
    public static function specification(string $aliasedField, string $value) : self
    {
        return new self($aliasedField, $value);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    final public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->andWhere("$this->aliasedField IN ($this->value)");
    }
    
    
    
}
