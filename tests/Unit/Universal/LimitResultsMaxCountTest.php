<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications\Universal;

use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Specification;
use Mediagone\Doctrine\Specifications\Universal\LimitResultsMaxCount;
use PHPUnit\Framework\TestCase;


/**
 * @covers \Mediagone\Doctrine\Specifications\Universal\LimitResultsMaxCount
 */
final class LimitResultsMaxCountTest extends TestCase
{
    public function test_implements_specification_interface(): void
    {
        self::assertInstanceOf(Specification::class, LimitResultsMaxCount::specification(1));
    }
    
    public function test_dont_accept_negative_count(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LimitResultsMaxCount::specification(-1);
    }
    
    public function test_dont_accept_zero_count(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LimitResultsMaxCount::specification(0);
    }
    
    
}
