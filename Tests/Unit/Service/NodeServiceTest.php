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

class NodeServiceTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\Workspace
	 */
	protected $workspaceMock;

	/**
	 * @var \TechDivision\Neos\Search\Service\NodeService
	 */
	protected $nodeService;

	/**
	 * @var array configuration
	 */
	protected $settings;

	public function setUp(){
		parent::setUp();
		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$this->nodeService = new \TechDivision\Neos\Search\Service\NodeService();
		$this->settings = array('ResultNodeType' => 'TYPO3.Neos.NodeTypes:Page');
	}

	private function getNodeRepositoryMock($node = null){
		$nodeRepositoryMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findOneByIdentifier'))->disableOriginalConstructor()->getMock();
		$nodeRepositoryMock->expects($this->any())->method('findOneByIdentifier')->will($this->returnValue($node));
		return $nodeRepositoryMock;
	}

	private function getNodeTypeMock($NodeTypeName){
		$NodeType = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\NodeType', array('getName'))->disableOriginalConstructor()->getMock();
		$NodeType->expects($this->any())->method('getName')->will($this->returnValue($NodeTypeName));
		return $NodeType;
	}
	private function getSingleNode($NodeTypeName, $isAccessible = false, $isVisible = false){
		$node = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array('getNodeType', 'isAccessible', 'isVisible'))->disableOriginalConstructor()->getMock();
		$node->expects($this->any())->method('getNodeType')->will($this->returnValue($this->getNodeTypeMock($NodeTypeName)));
		$node->expects($this->any())->method('isAccessible')->will($this->returnValue($isAccessible));
		$node->expects($this->any())->method('isVisible')->will($this->returnValue($isVisible));
		return $node;
	}

	public function testGetPageNodeWithWrongNode(){
		$this->inject($this->nodeService, 'nodeRepository', $this->getNodeRepositoryMock());
		$this->assertSame(null, $this->nodeService->getPageNode($this->getSingleNode('wrongNodeTypeName'), $this->workspaceMock));
	}

	public function testGetPageNodeWithCorrectNodeWithoutParent(){
		$node = $this->getSingleNode($this->settings['ResultNodeType']);
		$this->inject($this->nodeService, 'settings', $this->settings);
		$this->assertSame($node, $this->nodeService->getPageNode($node, $this->workspaceMock));
	}

	public function testGetPageNodeWithWrongNodeWithParent(){
		$nodeMock = $this->getSingleNode('NodeTypeName');
		$nodeRepositoryMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findOneByPath'))->disableOriginalConstructor()->getMock();
		// due recursive function, first call initialises first recursion, after second call return
		$nodeRepositoryMock->expects($this->at(0))->method('findOneByPath')->will($this->returnValue($nodeMock));
		$nodeRepositoryMock->expects($this->at(1))->method('findOneByPath')->will($this->returnValue(null));
		$this->inject($this->nodeService, 'nodeRepository', $nodeRepositoryMock);
		$node = $this->getSingleNode('WrongNodeType');
		$this->assertSame(null, $this->nodeService->getPageNode($node, $this->workspaceMock));
	}

	public function testGetPageNodeByIdentifierNotFound(){
		$this->inject($this->nodeService, 'nodeRepository', $this->getNodeRepositoryMock());
		//$this->assertSame(null, $this->nodeService->getPageNodeByNodeIdentifier('123id', $this->workspaceMock));
	}

	public function testGetPageNodeByIdentifierNodeFound(){
		$nodeRepositoryMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findOneByIdentifier'))->disableOriginalConstructor()->getMock();
		// due recursive function, first call initialises first recursion, after second call return
		$nodeRepositoryMock->expects($this->any())->method('findOneByIdentifier')->will($this->returnValue($this->getSingleNode('NodeType', true, true)));
		$this->inject($this->nodeService, 'nodeRepository', $nodeRepositoryMock);
		$this->assertSame(null, $this->nodeService->getPageNodeByNodeIdentifier('123id', $this->workspaceMock));
	}

	public function testGetPageNodeByIdentifierNodeFoundWithPageNode(){
		$nodeRepositoryMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findOneByIdentifier'))->disableOriginalConstructor()->getMock();
		// due recursive function, first call initialises first recursion, after second call return
		$nodeRepositoryMock->expects($this->any())->method('findOneByIdentifier')->will($this->returnValue($this->getSingleNode('NodeType', true, true)));

		$nodeServiceMock = $this->getMock('\TechDivision\Neos\Search\Service\NodeService', array('getPageNode'));
		$pageNodeMock = $this->getSingleNode('TYPO3.Neos.NodeTypes:Page', true, true);
		$nodeServiceMock->expects($this->any())->method('getPageNode')->will($this->returnValue($pageNodeMock));

		$this->inject($nodeServiceMock, 'nodeRepository', $nodeRepositoryMock);
		$this->assertSame($pageNodeMock, $nodeServiceMock->getPageNodeByNodeIdentifier('123id', $this->workspaceMock));
	}

	public function testCheckValidityOnCLI(){
		$this->assertSame(true, $this->nodeService->checkValidity($this->getSingleNode('dummy')));
	}

	public function testCheckValidityOnNotCLI(){
		$this->assertSame(false, $this->nodeService->checkValidity($this->getSingleNode('dummy'), 'WEB'));
	}

	public function testCheckValidityOnNotCLIWithSuitableNode(){
		$this->assertSame(true, $this->nodeService->checkValidity($this->getSingleNode('dummy', true, true), 'WEB'));
	}
}
?>