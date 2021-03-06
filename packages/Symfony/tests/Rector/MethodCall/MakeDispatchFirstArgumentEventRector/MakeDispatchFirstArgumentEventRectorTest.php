<?php declare(strict_types=1);

namespace Rector\Symfony\Tests\Rector\MethodCall\MakeDispatchFirstArgumentEventRector;

use Rector\Symfony\Rector\MethodCall\MakeDispatchFirstArgumentEventRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class MakeDispatchFirstArgumentEventRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/fixture.php.inc',
            __DIR__ . '/Fixture/event_class_constant.php.inc',
            __DIR__ . '/Fixture/keep_string_event_constant.php.inc',
        ]);
    }

    protected function getRectorClass(): string
    {
        return MakeDispatchFirstArgumentEventRector::class;
    }
}
