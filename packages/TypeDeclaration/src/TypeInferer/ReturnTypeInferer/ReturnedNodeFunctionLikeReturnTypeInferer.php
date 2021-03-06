<?php declare(strict_types=1);

namespace Rector\TypeDeclaration\TypeInferer\ReturnTypeInferer;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Rector\PhpParser\Node\Manipulator\FunctionLikeManipulator;
use Rector\TypeDeclaration\Contract\TypeInferer\FunctionLikeReturnTypeInfererInterface;
use Rector\TypeDeclaration\TypeInferer\AbstractTypeInferer;

final class ReturnedNodeFunctionLikeReturnTypeInferer extends AbstractTypeInferer implements FunctionLikeReturnTypeInfererInterface
{
    /**
     * @var FunctionLikeManipulator
     */
    private $functionLikeManipulator;

    public function __construct(FunctionLikeManipulator $functionLikeManipulator)
    {
        $this->functionLikeManipulator = $functionLikeManipulator;
    }

    /**
     * @param ClassMethod|Closure|Function_ $functionLike
     * @return string[]
     */
    public function inferFunctionLike(FunctionLike $functionLike): array
    {
        $resolvedReturnTypeInfo = $this->functionLikeManipulator->resolveStaticReturnTypeInfo($functionLike);

        return $resolvedReturnTypeInfo ? $resolvedReturnTypeInfo->getDocTypes() : [];
    }
}
