<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;


final class WhereFieldGreaterThanOrEqualTo extends WhereField
{
    
    public static function specification(string $aliasedField, string $paramName, $value, string $type) : self
    {
        return new self($aliasedField, $paramName, '>=', $value, $type);
    }
    
}
