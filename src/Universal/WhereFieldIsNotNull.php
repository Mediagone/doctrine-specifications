<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;


final class WhereFieldIsNotNull extends Specification
{
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private string $aliasedField;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    protected function __construct(string $aliasedField)
    {
        $this->aliasedField = $aliasedField;
    }
    
    
    public static function specification(string $aliasedField) : self
    {
        return new self($aliasedField);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->andWhere("$this->aliasedField IS NOT NULL");
    }
    
    
    
}
