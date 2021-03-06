<?php declare(strict_types=1);

namespace Rector\Php\Tests\Rector\FuncCall\RemoveExtraParametersRector\Source;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class MagicEventDispatcher
{
    /**
     * {@inheritdoc}
     *
     * @param string|null $eventName
     */
    public function dispatch($event/*, string $eventName = null*/)
    {
        $eventName = 1 < \func_num_args() ? \func_get_arg(1) : null;
    }
}
