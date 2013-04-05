<?php
namespace TechDivision\Neos\Search\Tests\Unit\Domain\Model;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class ResultTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\Domain\Model\Result
	 */
	protected $request;

	public function setUp(){
		parent::setUp();
		$this->request = new \TechDivision\Neos\Search\Domain\Model\Result();
	}

	public function testSetGetPageNode(){
		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array())->disableOriginalConstructor()->getMock();
		$this->request->setPageNode($nodeMock);
		$this->assertSame($nodeMock, $this->request->getPageNode($nodeMock));
	}

	public function testSetGetDocument(){
		$document = new \TechDivision\Search\Document\Document();
		$this->request->setDocument($document);
		$this->assertSame($document, $this->request->getDocument());
	}

	public function testSetGetNode(){
		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array())->disableOriginalConstructor()->getMock();
		$this->request->setNode($nodeMock);
		$this->assertSame($nodeMock, $this->request->getNode());
	}
}
?>