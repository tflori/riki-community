<?php

namespace Test\Unit\Service;

use App\Service\IpHelper;
use Test\TestCase;

class IpHelperTest extends TestCase
{
    /** @test */
    public function usesRegularExpressionMatching()
    {
        $ip = '192.168.0.23';
        $rangePattern = '192.168.0.\d+';
        $ipHelper = new IpHelper();

        $inRange = $ipHelper->isInRange($ip, $rangePattern);

        self::assertTrue($inRange);
    }

    /** @dataProvider provideIpsInRange
     * @test */
    public function comparesTheFirstBits(string $ip, string $range, bool $inRange)
    {
        $ipHelper = new IpHelper();

        $result = $ipHelper->isInRange($ip, $range);

        self::assertSame($inRange, $result);
    }

    public function provideIpsInRange()
    {
        return [
            ['192.168.0.1', '192.168.0.0/24', true],
            ['192.168.23.1', '192.168.0.0/16', true],
            ['192.168.23.1', '192.168.0.0/24', false],
            ['fe80::42', 'fe80::/64', true],
            ['fe80:a300::42', 'fe80::/64', false],
        ];
    }
}
