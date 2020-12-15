<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications\Universal;

use Mediagone\Doctrine\Specifications\Specification;
use Mediagone\Doctrine\Specifications\Universal\DebugDumpDQL;
use PHPUnit\Framework\TestCase;


final class DebugDumpDQLTest extends TestCase
{
    public function test_implements_specification_interface(): void
    {
        self::assertInstanceOf(Specification::class, DebugDumpDQL::specification());
    }
    
}
