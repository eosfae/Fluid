<?php
namespace TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\Expression;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\BooleanParser;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Ternary Condition Node - allows the shorthand version
 * of a condition to be written as `{var ? thenvar : elsevar}`
 */
class TernaryExpressionNode extends AbstractExpressionNode
{

    /**
     * Pattern which detects ternary conditions written in shorthand
     * syntax, e.g. {checkvar ? thenvar : elsevar}.
     */
    public static $detectionExpression = '/
		(
			{                                                               # Start of shorthand syntax
				(?:                                                         # Math expression is composed of...
					[\\!a-zA-Z0-9.\(\)\!\|\&\\\'\'\"\=\<\>\%\s\{\}\:\,]+    # Check variable side
					[\s]?\?[\s]?
					[a-zA-Z0-9.\s\'\"\\.]*                                  # Then variable side, optional
					[\s]?:[\s]?
					[a-zA-Z0-9.\s\'\"\\.]+                                  # Else variable side
				)
			}                                                               # End of shorthand syntax
		)/x';

    /**
     * Filter out variable names form expression
     */
    protected static $variableDetection = '/[^\'a-zA-Z0-9\.\\\\]{0,1}([a-zA-Z0-9\.\\\\]*)[^\']{0,1}/';

    /**
     * @param RenderingContextInterface $renderingContext
     * @param string $expression
     * @param array $matches
     * @return mixed
     */
    public static function evaluateExpression(RenderingContextInterface $renderingContext, $expression, array $matches)
    {
        $parts = preg_split('/([\?:])/s', $expression);
        $parts = array_map([__CLASS__, 'trimPart'], $parts);
        $negated = false;
        list ($check, $then, $else) = $parts;

        if ($then === '') {
            $then = $check{0} === '!' ? $else : $check;
        }

        $context = static::gatherContext($renderingContext, $expression);

        $parser = new BooleanParser();
        $checkResult = $parser->evaluate($check, $context);

        if ($checkResult) {
            return static::getTemplateVariableOrValueItself($renderingContext->getTemplateParser()->unquoteString($then), $renderingContext);
        } else {
            return static::getTemplateVariableOrValueItself($renderingContext->getTemplateParser()->unquoteString($else), $renderingContext);
        }
    }

    /**
     * @param mixed $candidate
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function getTemplateVariableOrValueItself($candidate, RenderingContextInterface $renderingContext)
    {
        $suspect = parent::getTemplateVariableOrValueItself($candidate, $renderingContext);
        if ($suspect === $candidate) {
            return $renderingContext->getTemplateParser()->unquoteString($suspect);
        }
        return $suspect;
    }

    /**
     * Gather all context variables used in the expression
     *
     * @param RenderingContextInterface $renderingContext
     * @param string $expression
     * @return array
     */
    public static function gatherContext($renderingContext, $expression)
    {
        $context = [];
        if (preg_match_all(static::$variableDetection, $expression, $matches) > 0) {
            foreach ($matches[1] as $variable) {
                if (strtolower($variable) == 'true' || strtolower($variable) == 'false' || empty($variable) === true) {
                    continue;
                }
                $context[$variable] = static::getTemplateVariableOrValueItself($variable, $renderingContext);
            }
        }
        return $context;
    }
}
