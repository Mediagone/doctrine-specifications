<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications\Universal;

use Mediagone\Doctrine\Specifications\Specification;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpSQL;
use PHPUnit\Framework\TestCase;


/**
 * @covers \Mediagone\Doctrine\Specifications\Universal\DebugDumpSQL
 */
final class DebugDumpSQLTest extends TestCase
{
    public function test_implements_specification_interface(): void
    {
        self::assertInstanceOf(Specification::class, DebugDumpSQL::specification());
    }
    
}
