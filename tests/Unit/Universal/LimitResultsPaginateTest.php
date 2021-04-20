<?php declare(strict_types=1);

namespace Tests\Mediagone\Doctrine\Specifications\Universal;

use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Specification;
use Mediagone\Doctrine\Specifications\Universal\LimitResultsPaginate;
use PHPUnit\Framework\TestCase;


/**
 * @covers \Mediagone\Doctrine\Specifications\Universal\LimitResultsPaginate
 */
final class LimitResultsPaginateTest extends TestCase
{
    public function test_implements_specification_interface(): void
    {
        self::assertInstanceOf(Specification::class, LimitResultsPaginate::specification(0, 10));
    }
    
    
    public function test_dont_accept_negative_page_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LimitResultsPaginate::specification(-1, 10);
    }
    
    
    public function test_dont_accept_zero_items_per_page(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LimitResultsPaginate::specification(0, 0);
    }
    
    
    public function test_dont_accept_negative_items_per_page(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LimitResultsPaginate::specification(0, -1);
    }
    
    
    
}
