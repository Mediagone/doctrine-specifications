<?php declare(strict_types=1);

namespace Tests\Mediagone\DDD\Doctrine\Specifications\Universal;

use Mediagone\DDD\Doctrine\Specifications\Specification;
use Mediagone\DDD\Doctrine\Specifications\Universal\DebugDumpSQL;
use PHPUnit\Framework\TestCase;


final class DebugDumpSQLTest extends TestCase
{
    public function test_implements_specification_interface(): void
    {
        self::assertInstanceOf(Specification::class, DebugDumpSQL::specification());
    }
    
}
