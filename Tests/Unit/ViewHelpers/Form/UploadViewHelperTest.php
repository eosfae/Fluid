<?php
declare(ENCODING = 'utf-8');
namespace F3\Fluid\ViewHelpers\Form;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

include_once(__DIR__ . '/Fixtures/EmptySyntaxTreeNode.php');
include_once(__DIR__ . '/Fixtures/Fixture_UserDomainClass.php');
require_once(__DIR__ . '/../ViewHelperBaseTestcase.php');

/**
 * Test for the "Upload" Form view helper
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class UploadViewHelperTest extends \F3\Fluid\ViewHelpers\ViewHelperBaseTestcase {

	/**
	 * var \F3\Fluid\ViewHelpers\Form\UploadViewHelper
	 */
	protected $viewHelper;

	public function setUp() {
		parent::setUp();
		$this->viewHelper = $this->getMock($this->buildAccessibleProxy('F3\Fluid\ViewHelpers\Form\UploadViewHelper'), array('setErrorClassAttribute', 'registerFieldNameForFormTokenGeneration'));
		$this->injectDependenciesIntoViewHelper($this->viewHelper);
		$this->viewHelper->initializeArguments();
	}

	/**
	 * @test
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	public function renderCorrectlySetsTagName() {
		$mockTagBuilder = $this->getMock('F3\Fluid\Core\ViewHelper\TagBuilder', array('setTagName'), array(), '', FALSE);
		$mockTagBuilder->expects($this->once())->method('setTagName')->with('input');
		$this->viewHelper->injectTagBuilder($mockTagBuilder);
		$this->viewHelper->setArguments(new \F3\Fluid\Core\ViewHelper\Arguments(array()));

		$this->viewHelper->initialize();
		$this->viewHelper->render();
	}

	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	public function renderCorrectlySetsTypeNameAndValueAttributes() {
		$mockTagBuilder = $this->getMock('F3\Fluid\Core\ViewHelper\TagBuilder', array('addAttribute', 'setContent', 'render'), array(), '', FALSE);
		$mockTagBuilder->expects($this->at(0))->method('addAttribute')->with('type', 'file');
		$mockTagBuilder->expects($this->at(1))->method('addAttribute')->with('name', 'someName');
		$this->viewHelper->expects($this->once())->method('registerFieldNameForFormTokenGeneration')->with('someName');
		$mockTagBuilder->expects($this->once())->method('render');
		$this->viewHelper->injectTagBuilder($mockTagBuilder);

		$arguments = new \F3\Fluid\Core\ViewHelper\Arguments(array(
			'name' => 'someName',
		));

		$this->viewHelper->setArguments($arguments);
		$this->viewHelper->setViewHelperNode(new \F3\Fluid\ViewHelpers\Fixtures\EmptySyntaxTreeNode());
		$this->viewHelper->initialize();
		$this->viewHelper->render();
	}

	/**
	 * @test
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	public function renderCallsSetErrorClassAttribute() {
		$this->viewHelper->expects($this->once())->method('setErrorClassAttribute');
		$this->viewHelper->render();
	}
}

?>