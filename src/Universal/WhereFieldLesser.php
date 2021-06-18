<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;


class WhereFieldLesser extends WhereField
{
    
    public static function specification(string $aliasedField, string $paramName, $value, ?string $paramType = null) : self
    {
        return new self($aliasedField, $paramName, '<', $value, $paramType);
    }
    
}
