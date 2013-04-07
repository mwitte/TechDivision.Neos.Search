<?php

namespace TechDivision\Search\Tests\Unit\Field\Result;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class NodeResultFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\Factory\Result\NodeResultFactory
	 */
	protected $nodeResultFactory;

	public function setUp(){
		parent::setUp();
		$this->nodeResultFactory = new \TechDivision\Neos\Search\Factory\Result\NodeResultFactory();
		$nodeServiceMock = $this->getMockBuilder('\TechDivision\Neos\Search\Service\NodeService')->disableOriginalConstructor()->getMock();
		$this->inject($this->nodeResultFactory, 'nodeService', $nodeServiceMock);
	}

	private function getNodeTypeMock($NodeTypeName){
		$NodeType = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\NodeType', array('getName'))->disableOriginalConstructor()->getMock();
		$NodeType->expects($this->any())->method('getName')->will($this->returnValue($NodeTypeName));
		return $NodeType;
	}
	private function getSingleNode($NodeTypeName){
		$node = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array('getNodeType'))->disableOriginalConstructor()->getMock();
		$node->expects($this->any())->method('getNodeType')->will($this->returnValue($this->getNodeTypeMock($NodeTypeName)));
		return $node;
	}

	public function testCreateResultFromNodeDocumentWithoutIdentifierField(){
		$document = new \TechDivision\Search\Document\Document();
		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$configuration = array();
		$configuration['Schema']['DocumentIdentifierField'] = "id";
		$this->inject($this->nodeResultFactory, 'settings', $configuration);
		$this->assertEquals(null, $this->nodeResultFactory->createResultFromNodeDocument($document, $workspaceMock));
	}

	public function testCreateResultFromNodeDocumentNotFoundPageNode(){
		$nodeServiceMock = $this->getMockBuilder('\TechDivision\Neos\Search\Service\NodeService')->disableOriginalConstructor()->getMock();
		$this->inject($this->nodeResultFactory, 'nodeService', $nodeServiceMock);

		$document = new \TechDivision\Search\Document\Document();
		$field = new \TechDivision\Search\Field\Field('id', 'idname');
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
		$nodeServiceMock = $this->getMockBuilder('\TechDivision\Neos\Search\Service\NodeService', array('getPageNodeByNodeIdentifier'))->disableOriginalConstructor()->getMock();
		$nodeServiceMock->expects($this->any())->method('getPageNodeByNodeIdentifier')->will($this->returnValue($nodeMock));
		$this->inject($this->nodeResultFactory, 'nodeService', $nodeServiceMock);

		$document = new \TechDivision\Search\Document\Document();
		$field = new \TechDivision\Search\Field\Field('id', 'idname');
		$document->addField($field);

		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$configuration = array();
		$configuration['Schema']['DocumentIdentifierField'] = "id";
		$this->inject($this->nodeResultFactory, 'settings', $configuration);

		$result = new \TechDivision\Neos\Search\Domain\Model\Result();
		$result->setPageNode($nodeMock);
		$result->setDocument($document);

		$this->assertEquals($result, $this->nodeResultFactory->createResultFromNodeDocument($document, $workspaceMock));
	}
}
?>