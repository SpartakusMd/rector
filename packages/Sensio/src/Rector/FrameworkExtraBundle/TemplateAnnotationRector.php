<?php declare(strict_types=1);

namespace Rector\Sensio\Rector\FrameworkExtraBundle;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\NodeTypeResolver\PhpDoc\NodeAnalyzer\DocBlockManipulator;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;
use Rector\Sensio\Helper\TemplateGuesser;

final class TemplateAnnotationRector extends AbstractRector
{
    /**
     * @var int
     */
    private $version;

    /**
     * @var DocBlockManipulator
     */
    private $docBlockManipulator;

    /**
     * @var TemplateGuesser
     */
    private $templateGuesser;

    public function __construct(
        DocBlockManipulator $docBlockManipulator,
        TemplateGuesser $templateGuesser,
        int $version = 3
    ) {
        $this->docBlockManipulator = $docBlockManipulator;
        $this->templateGuesser = $templateGuesser;
        $this->version = $version;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition(
            'Turns `@Template` annotation to explicit method call in Controller of FrameworkExtraBundle in Symfony',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
/**
 * @Template()
 */
public function indexAction()
{
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
public function indexAction()
{
    return $this->render("index.html.twig");
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->docBlockManipulator->hasTag($node, 'Template')) {
            return null;
        }

        /** @var Return_|null $returnNode */
        $returnNode = $this->betterNodeFinder->findLastInstanceOf((array) $node->stmts, Return_::class);

        // create "$this->render('template.file.twig.html', ['key' => 'value']);" method call
        $renderArguments = $this->resolveRenderArguments($node, $returnNode);
        $thisRenderMethodCall = $this->createMethodCall('this', 'render', $renderArguments);

        if ($returnNode === null) {
            // or add as last statement in the method
            $node->stmts[] = new Return_($thisRenderMethodCall);
        }

        // replace Return_ node value if exists and is not already in correct format
        if ($returnNode && ! $returnNode->expr instanceof MethodCall) {
            $returnNode->expr = $thisRenderMethodCall;
        }

        // remove annotation
        $this->docBlockManipulator->removeTagFromNode($node, 'Template');

        return $node;
    }

    /**
     * @return Arg[]
     */
    private function resolveRenderArguments(ClassMethod $classMethod, ?Return_ $returnNode): array
    {
        $arguments = [$this->resolveTemplateName($classMethod)];
        if ($returnNode === null) {
            return $this->createArgs($arguments);
        }

        if ($returnNode->expr instanceof Array_ && count($returnNode->expr->items)) {
            $arguments[] = $returnNode->expr;
        }

        $arguments = array_merge($arguments, $this->resolveArgumentsFromMethodCall($returnNode));

        return $this->createArgs($arguments);
    }

    private function resolveTemplateName(ClassMethod $classMethod): string
    {
        $templateTag = $this->docBlockManipulator->getTagByName($classMethod, 'Template');
        $content = (string) $templateTag;

        $annotationContent = Strings::match($content, '#\(("|\')(?<filename>.*?)("|\')\)#');
        if (isset($annotationContent['filename'])) {
            return $annotationContent['filename'];
        }

        return $this->templateGuesser->resolveFromClassMethodNode($classMethod, $this->version);
    }

    /**
     * Already existing method call
     * @return mixed[]
     */
    private function resolveArgumentsFromMethodCall(Return_ $returnNode): array
    {
        $arguments = [];
        if ($returnNode->expr instanceof MethodCall) {
            foreach ($returnNode->expr->args as $arg) {
                if ($arg->value instanceof Array_) {
                    $arguments[] = $arg->value;
                }
            }
        }

        return $arguments;
    }
}
