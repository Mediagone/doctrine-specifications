<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;


final class WhereFieldEqualTo extends WhereField
{
    
    public static function specification(string $aliasedField, string $paramName, $value, string $paramType) : self
    {
        return new self($aliasedField, $paramName, '=', $value, $paramType);
    }
    
}
