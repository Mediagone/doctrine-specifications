<?php declare(strict_types=1);

namespace Tests\Mediagone\DDD\Doctrine\Specifications\Universal;

use InvalidArgumentException;
use Mediagone\DDD\Doctrine\Specifications\Specification;
use Mediagone\DDD\Doctrine\Specifications\Universal\LimitResultsOffset;
use PHPUnit\Framework\TestCase;


final class LimitResultsOffsetTest extends TestCase
{
    public function test_implements_specification_interface(): void
    {
        self::assertInstanceOf(Specification::class, LimitResultsOffset::specification(0));
    }
    
    public function test_dont_accept_negative_count(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LimitResultsOffset::specification(-1);
    }
    
}
