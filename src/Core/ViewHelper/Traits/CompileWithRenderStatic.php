<?php
namespace TYPO3Fluid\Fluid\Core\ViewHelper\Traits;

/**
 * Class CompilableWithRenderStatic
 *
 * Provides default methods for rendering and compiling
 * any ViewHelper that conforms to the `renderStatic`
 * method pattern.
 */
trait CompileWithRenderStatic
{

    /**
     * Default render method - simply calls renderStatic() with a
     * prepared set of arguments.
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
     * @return \Closure
     */
    protected abstract function buildRenderChildrenClosure();
}
