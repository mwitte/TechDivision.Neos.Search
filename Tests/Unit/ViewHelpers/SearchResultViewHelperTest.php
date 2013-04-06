<?php

namespace TechDivision\Search\Tests\Unit\ViewHelpers;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class SearchResultViewHelperTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\ViewHelpers\SearchResultViewHelper
	 */
	protected $searchResultViewHelper;

	public function setUp(){
		parent::setUp();
		$this->searchResultViewHelper = $this->getMock('\TechDivision\Neos\Search\ViewHelpers\SearchResultViewHelper', array('renderChildren'));
		$this->searchResultViewHelper->expects($this->any())->method('renderChildren')->will($this->returnValue(null));
		$settings = array();
		$settings['SearchResult']['Highlight']['prefix'] = '<b>';
		$settings['SearchResult']['Highlight']['suffix'] = '</b>';
		$this->inject($this->searchResultViewHelper, 'settings', $settings);
	}

	public function testRenderEmpty(){
		$this->assertSame(null, $this->searchResultViewHelper->render());
	}

	/**
	 * @depends testRenderEmpty
	 */
	public function testRenderSimpleString(){
		$this->assertSame('test', $this->searchResultViewHelper->render('test'));
	}

	/**
	 * @depends testRenderSimpleString
	 */
	public function testRenderHtmlString(){
		$this->assertSame('test', $this->searchResultViewHelper->render('<div>test</div>'));
	}

	/**
	 * @depends testRenderSimpleString
	 */
	public function testRenderStringWithTokenNotFound(){
		$this->assertSame('test token not found', $this->searchResultViewHelper->render('test token not found', 'notFoundToken'));
	}

	public function testRenderStringWithTokenFound(){
		$this->assertSame('test <b>token</b> found', $this->searchResultViewHelper->render('test token found', 'token'));
	}

	public function testRenderHtmlStringWithTokenFound(){
		$this->assertSame('test <b>token</b> found', $this->searchResultViewHelper->render('test <span>token found</span>', 'token'));
	}
}
?>