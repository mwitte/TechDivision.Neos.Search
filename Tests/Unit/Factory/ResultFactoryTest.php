<?php

namespace TechDivision\Search\Tests\Unit\Field;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class ResultFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\Factory\ResultFactory
	 */
	protected $resultFactory;

	/**
	 * @var \TechDivision\Search\Document\Document
	 */
	protected $document;

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\Workspace
	 */
	protected $workspaceMock;

	/**
	 * @var \TechDivision\Neos\Search\Domain\Model\Result
	 */
	protected $request;

	public function setUp(){
		parent::setUp();
		$this->resultFactory = new \TechDivision\Neos\Search\Factory\ResultFactory();
		$this->document = new \TechDivision\Search\Document\Document();
		$this->configuration = array(
			'Schema' => array(
				'PageNodeIdentifier' => 'pageId',
				'DocumentTypeField' => 'contentType'
			)
		);
		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$nodeResultFactory = $this->getMockBuilder('\TechDivision\Neos\Search\Factory\Result\NodeResultFactory', array('createResultFromNodeDocument'))->disableOriginalConstructor()->getMock();
		$this->request = new \TechDivision\Neos\Search\Domain\Model\Result();
		$nodeResultFactory->expects($this->any())->method('createResultFromNodeDocument')->will($this->returnValue($this->request));
		$this->inject($this->resultFactory, 'nodeResultFactory', $nodeResultFactory);
		$this->inject($this->resultFactory, 'settings', $this->configuration);
	}

	public function testCreateFromDocumentWithoutField(){
		$this->assertSame(null, $this->resultFactory->createFromDocument($this->document, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromDocumentWithoutField
	 */
	public function testCreateFromDocumentWithFieldWrongValue(){
		$field = new \TechDivision\Search\Field\Field('contentType', 'wrongValue');
		$this->document->addField($field);
		$this->assertSame(null, $this->resultFactory->createFromDocument($this->document, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromDocumentWithFieldWrongValue
	 */
	public function testCreateFromDocumentWithValidDocument(){
		$field = new \TechDivision\Search\Field\Field('contentType', 'TYPO3-TYPO3CR-Domain-Model-Node');
		$this->document->addField($field);
		$this->assertSame($this->request, $this->resultFactory->createFromDocument($this->document, $this->workspaceMock));
	}

	public function testCreateMultipleWithoutDocuments(){
		$this->assertSame(array(), $this->resultFactory->createMultipleFromDocuments(array(), $this->workspaceMock));
	}

	/**
	 * @depends testCreateMultipleWithoutDocuments
	 */
	public function testCreateMultipleWithWrongDocument(){
		$documents = array($this->document);
		$this->assertSame(array(), $this->resultFactory->createMultipleFromDocuments($documents, $this->workspaceMock));
	}

	/**
	 * @depends testCreateMultipleWithoutDocuments
	 */
	public function testCreateMultipleWithValidDocument(){
		$field = new \TechDivision\Search\Field\Field('contentType', 'TYPO3-TYPO3CR-Domain-Model-Node');
		$this->document->addField($field);
		$this->document->addField(new \TechDivision\Search\Field\Field('pageId', ''));
		$documents = array($this->document);
		$this->assertSame(array($this->request), $this->resultFactory->createMultipleFromDocuments($documents, $this->workspaceMock));
	}
}
?>