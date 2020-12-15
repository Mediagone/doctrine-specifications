<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications\Universal;

use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Specification;
use Mediagone\Doctrine\Specifications\Universal\LimitResultsOffset;
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
