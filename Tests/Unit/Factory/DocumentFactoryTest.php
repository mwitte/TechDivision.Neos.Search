<?php

namespace TechDivision\Search\Tests\Unit\Field;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class DocumentFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\Factory\DocumentFactory
	 */
	protected $documentFactory;

	/**
	 * @var \TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory
	 */
	protected $nodeDocumentFactoryMock;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\Workspace
	 */
	protected $workspaceMock;

	public function setUp(){
		parent::setUp();
		$this->documentFactory = new \TechDivision\Neos\Search\Factory\DocumentFactory();
		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$this->nodeDocumentFactoryMock = $this->getMockBuilder('\TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory', array('getAllDocuments'))->disableOriginalConstructor()->getMock();
		$this->nodeDocumentFactoryMock->expects($this->any())->method('getAllDocuments')->will($this->returnValue(array()));
	}

	public function testGetAllDocuments(){
		$this->inject($this->documentFactory, 'nodeDocumentFactory', $this->nodeDocumentFactoryMock);
		$this->assertSame(array(), $this->documentFactory->getAllDocuments($this->workspaceMock, array()));
	}
}
?>