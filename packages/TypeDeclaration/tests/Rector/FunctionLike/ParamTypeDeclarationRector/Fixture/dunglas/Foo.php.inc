<?php

namespace Rector\TypeDeclaration\Tests\Rector\ClassMethod\ParamTypeDeclarationRector\Fixture\Dunglas;

/**
 * Foo.
 */
class Foo
{
    public function bar($foo)
    {
    }
    /**
     * @param float $a
     */
    public function test($a)
    {
        $closure = function ($a, $c) {
        };
    }
}
?>
-----
<?php

namespace Rector\TypeDeclaration\Tests\Rector\ClassMethod\ParamTypeDeclarationRector\Fixture\Dunglas;

/**
 * Foo.
 */
class Foo
{
    public function bar($foo)
    {
    }
    /**
     * @param float $a
     */
    public function test(float $a)
    {
        $closure = function ($a, $c) {
        };
    }
}
?>
