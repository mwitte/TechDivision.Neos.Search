<?php

namespace Com\TechDivision\Search\Tests\Unit\Field\Result;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class NodeResultFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\Result\NodeResultFactory
	 */
	protected $nodeResultFactory;

	public function setUp(){
		parent::setUp();
		$this->nodeResultFactory = new \Com\TechDivision\Neos\Search\Factory\Result\NodeResultFactory();
		$nodeServiceMock = $this->getMockBuilder('\Com\TechDivision\Neos\Search\Service\NodeService')->disableOriginalConstructor()->getMock();
		$this->inject($this->nodeResultFactory, 'nodeService', $nodeServiceMock);
	}

	private function getContentTypeMock($contentTypeName){
		$contentType = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\ContentType', array('getName'))->disableOriginalConstructor()->getMock();
		$contentType->expects($this->any())->method('getName')->will($this->returnValue($contentTypeName));
		return $contentType;
	}
	private function getSingleNode($contentTypeName){
		$node = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array('getContentType'))->disableOriginalConstructor()->getMock();
		$node->expects($this->any())->method('getContentType')->will($this->returnValue($this->getContentTypeMock($contentTypeName)));
		return $node;
	}

	public function testCreateResultFromNodeDocumentWithoutIdentifierField(){
		$document = new \Com\TechDivision\Search\Document\Document();
		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$configuration = array();
		$configuration['Schema']['DocumentIdentifierField'] = "id";
		$this->inject($this->nodeResultFactory, 'settings', $configuration);
		$this->assertEquals(null, $this->nodeResultFactory->createResultFromNodeDocument($document, $workspaceMock));
	}

	public function testCreateResultFromNodeDocumentNotFoundPageNode(){
		$nodeServiceMock = $this->getMockBuilder('\Com\TechDivision\Neos\Search\Service\NodeService')->disableOriginalConstructor()->getMock();
		$this->inject($this->nodeResultFactory, 'nodeService', $nodeServiceMock);

		$document = new \Com\TechDivision\Search\Document\Document();
		$field = new \Com\TechDivision\Search\Field\Field('id', 'idname');
		$document->addField($field);

		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$configuration = array();
		$configuration['Schema']['DocumentIdentifierField'] = "id";
		$this->inject($this->nodeResultFactory, 'settings', $configuration);

		$this->assertEquals(null, $this->nodeResultFactory->createResultFromNodeDocument($document, $workspaceMock));
	}

	public function testCreateResultFromNodeDocumentWithFoundPageNode(){

		$nodeRepositoryMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findOneByIdentifier'))->disableOriginalConstructor()->getMock();
		$nodeRepositoryMock->expects($this->any())->method('findOneByIdentifier')->will($this->returnValue(null));
		$this->inject($this->nodeResultFactory, 'nodeRepository', $nodeRepositoryMock);

		$nodeMock = $this->getSingleNode('name');
		$nodeServiceMock = $this->getMockBuilder('\Com\TechDivision\Neos\Search\Service\NodeService', array('getPageNodeByNodeIdentifier'))->disableOriginalConstructor()->getMock();
		$nodeServiceMock->expects($this->any())->method('getPageNodeByNodeIdentifier')->will($this->returnValue($nodeMock));
		$this->inject($this->nodeResultFactory, 'nodeService', $nodeServiceMock);

		$document = new \Com\TechDivision\Search\Document\Document();
		$field = new \Com\TechDivision\Search\Field\Field('id', 'idname');
		$document->addField($field);

		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$configuration = array();
		$configuration['Schema']['DocumentIdentifierField'] = "id";
		$this->inject($this->nodeResultFactory, 'settings', $configuration);

		$result = new \Com\TechDivision\Neos\Search\Domain\Model\Result();
		$result->setPageNode($nodeMock);
		$result->setDocument($document);

		$this->assertEquals($result, $this->nodeResultFactory->createResultFromNodeDocument($document, $workspaceMock));
	}
}
?>