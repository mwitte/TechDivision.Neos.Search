<?php

namespace TechDivision\Search\Tests\Unit\Aspect;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class PluginControllerTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\Controller\PluginController
	 */
	protected $pluginController;

	public function setUp(){
		parent::setUp();
		$this->pluginController = new \TechDivision\Neos\Search\Controller\PluginController();
	}

	public function testIndexAction(){
		$viewMock = $this->getMockBuilder('\TYPO3\Flow\Mvc\View\ViewInterface', array())->disableOriginalConstructor()->getMock();
		$this->inject($this->pluginController, 'view', $viewMock);
		$this->assertSame(null, $this->pluginController->indexAction());
	}

	public function testSearchAction(){
		$viewMock = $this->getMockBuilder('\TYPO3\Flow\Mvc\View\ViewInterface', array())->disableOriginalConstructor()->getMock();
		$this->inject($this->pluginController, 'view', $viewMock);
		$searchProvider = $this->getMock('\TechDivision\Neos\Search\Provider\SearchProvider', array('search'));
		$searchProvider->expects($this->once())->method('search')->will($this->returnValue(array()));
		$this->inject($this->pluginController, 'searchProvider', $searchProvider);
		$this->assertSame(null, $this->pluginController->searchAction(new \TechDivision\Neos\Search\Domain\Model\Request()));
	}
}
?>