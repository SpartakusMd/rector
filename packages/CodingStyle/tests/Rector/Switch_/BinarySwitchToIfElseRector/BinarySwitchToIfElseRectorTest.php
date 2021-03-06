<?php declare(strict_types=1);

namespace Rector\CodingStyle\Tests\Rector\Switch_\BinarySwitchToIfElseRector;

use Rector\CodingStyle\Rector\Switch_\BinarySwitchToIfElseRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class BinarySwitchToIfElseRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/fixture.php.inc',
            __DIR__ . '/Fixture/in_class.php.inc',
            __DIR__ . '/Fixture/if_or.php.inc',
            __DIR__ . '/Fixture/extra_break.php.inc',
        ]);
    }

    public function getRectorClass(): string
    {
        return BinarySwitchToIfElseRector::class;
    }
}
