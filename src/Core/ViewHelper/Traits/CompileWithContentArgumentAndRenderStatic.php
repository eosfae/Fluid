<?php
namespace TYPO3Fluid\Fluid\Core\ViewHelper\Traits;

use TYPO3Fluid\Fluid\Core\Exception;

/**
 * Class CompilableWithContentArgumentAndRenderStatic
 *
 * Provides default methods for rendering and compiling
 * any ViewHelper that conforms to the `renderStatic`
 * method pattern but has the added common use case that
 * an argument value must be checked and used instead of
 * the normal render children closure, if that named
 * argument is specified and not empty.
 */
trait CompileWithContentArgumentAndRenderStatic
{
    /**
     * Name of variable that contains the value to use
     * instead of render children closure, if specified.
     * If no name is provided here, the first variable
     * registered in `initializeArguments` of the ViewHelper
     * will be used.
     *
     * Note: it is significantly better practice to define
     * this property in your ViewHelper class and so fix it
     * to one particular argument instead of resolving,
     * especially when your ViewHelper is called multiple
     * times within an uncompiled template!
     *
     * @var string
     */
    protected $contentArgumentName;

    /**
     * Default render method to render ViewHelper with
     * first defined optional argument as content.
     *
     * @return mixed Rendered result
     * @api
     */
    public function render()
    {
        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * Helper which is mostly needed when calling renderStatic() from within
     * render().
     *
     * No public API yet.
     *
     * @return \Closure
     */
    protected function buildRenderChildrenClosure()
    {
        $argumentName = $this->resolveContentArgumentName();
        $arguments = $this->arguments;
        if (!empty($argumentName) && isset($arguments[$argumentName])) {
            $renderChildrenClosure = function () use ($arguments, $argumentName) {
                return $arguments[$argumentName];
            };
        } else {
            $self = clone $this;
            $renderChildrenClosure = function () use ($self) {
                return $self->renderChildren();
            };
        }
        return $renderChildrenClosure;
    }

    /**
     * @return string
     */
    protected function resolveContentArgumentName()
    {
        if (empty($this->contentArgumentName)) {
            $registeredArguments = call_user_func_array([$this, 'prepareArguments'], []);
            foreach ($registeredArguments as $registeredArgument) {
                if (!$registeredArgument->isRequired()) {
                    $this->contentArgumentName = $registeredArgument->getName();
                    return $this->contentArgumentName;
                }
            }
            throw new Exception(
                sprintf('Attempting to compile %s failed. Chosen compile method requires that ViewHelper has ' .
                'at least one registered and optional argument', __CLASS__)
            );
        }
        return $this->contentArgumentName;
    }
}
