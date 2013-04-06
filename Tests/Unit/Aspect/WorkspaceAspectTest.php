<?php

namespace TechDivision\Search\Tests\Unit\Aspect;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class WorkspaceAspectTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\Aspect\WorkspaceAspect
	 */
	protected $workspaceAspect;

	public function setUp(){
		parent::setUp();
		$this->workspaceAspect = new \TechDivision\Neos\Search\Aspect\WorkspaceAspect();
		$searchProviderMock = $this->getMock('\TechDivision\Neos\Search\Provider\SearchProvider', array('providerNeedsInputDocuments', 'updateDocument'));
		$searchProviderMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(true));
		$searchProviderMock->expects($this->any())->method('updateDocument')->will($this->returnValue(true));
		$this->inject($this->workspaceAspect, 'searchProvider', $searchProviderMock);
		$this->inject($this->workspaceAspect, 'settings', array('Workspace' => 'live'));
	}

	/**
	 * Returns a joinPoint, one param for every call
	 *
	 * @param mixed $firstReturn
	 * @param mixed $secondReturn
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getJoinPoint($firstReturn = null, $secondReturn = null){
		$joinPointMock = $this->getMock('\TYPO3\Flow\AOP\JoinPointInterface', array('getMethodArgument'));
		if($secondReturn !== null){
			$joinPointMock->expects($this->any())->method('getMethodArgument')->will($this->onConsecutiveCalls($firstReturn, $secondReturn));
		}elseif($firstReturn !== null){
			$joinPointMock->expects($this->any())->method('getMethodArgument')->will($this->onConsecutiveCalls($firstReturn));
		}
		return $joinPointMock;
	}

	public function testPublishNodesProviderNeedsNoUpdate(){
		$searchProviderMock = $this->getMock('\TechDivision\Neos\Search\Provider\SearchProvider', array('providerNeedsInputDocuments'));
		$searchProviderMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(false));
		// overwrite default from setUp
		$this->inject($this->workspaceAspect, 'searchProvider', $searchProviderMock);

		$this->assertSame(null, $this->workspaceAspect->publishNodes($this->getJoinPoint()));
	}

	/**
	 * @depends testPublishNodesProviderNeedsNoUpdate
	 */
	public function testPublishNodesThrowsException(){
		$joinPointMock = $this->getMock('\TYPO3\Flow\AOP\JoinPointInterface', array('getMethodArgument'));
		$joinPointMock->expects($this->any())->method('getMethodArgument')->will($this->throwException(new \TechDivision\Neos\Search\Exception\UpdatePublishingNodeException()));
		try{
			$this->workspaceAspect->publishNodes($joinPointMock);
		}catch (\TechDivision\Neos\Search\Exception\UpdatePublishingNodeException $e){
		}
		$this->assertEquals(new \TechDivision\Neos\Search\Exception\UpdatePublishingNodeException(), $e);
	}

	/**
	 * @depends testPublishNodesThrowsException
	 */
	public function testPublishNodesWrongWorkspace(){
		$joinPoint = $this->getJoinPoint('wrongWorkspace');
		$this->assertSame(null, $this->workspaceAspect->publishNodes($joinPoint));
	}

	/**
	 * @depends testPublishNodesWrongWorkspace
	 */
	public function testPublishNodesWithoutNodes(){
		$workspaceRepositoryMock = $this->getMock('\TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository', array('findByName'));
		$queryResultMock = $this->getMockBuilder('\TYPO3\Flow\Persistence\Generic\QueryResult', array('getFirst'))->disableOriginalConstructor()->getMock();
		$queryResultMock->expects($this->any())->method('getFirst')->will($this->returnValue(null));
		$workspaceRepositoryMock->expects($this->any())->method('findByName')->will($this->returnValue($queryResultMock));

		$joinPoint = $this->getJoinPoint('live', array());
		$this->inject($this->workspaceAspect, 'workspaceRepository', $workspaceRepositoryMock);

		$this->assertSame(0, $this->workspaceAspect->publishNodes($joinPoint));
	}

	public function testPublishNodesWithNodes(){
		$workspaceRepositoryMock = $this->getMock('\TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository', array('findByName'));
		$queryResultMock = $this->getMockBuilder('\TYPO3\Flow\Persistence\Generic\QueryResult', array('getFirst'))->disableOriginalConstructor()->getMock();
		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$queryResultMock->expects($this->any())->method('getFirst')->will($this->returnValue($workspaceMock));
		$workspaceRepositoryMock->expects($this->any())->method('findByName')->will($this->returnValue($queryResultMock));

		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node')->disableOriginalConstructor()->getMock();

		$joinPoint = $this->getJoinPoint('live', array($nodeMock));
		$this->inject($this->workspaceAspect, 'workspaceRepository', $workspaceRepositoryMock);

		$nodeDocumentFactoryMock = $this->getMock('\TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory', array('createFromNode'));
		$nodeDocumentFactoryMock->expects($this->any())->method('createFromNode')->will($this->returnValue(new \TechDivision\Search\Document\Document()));
		$this->inject($this->workspaceAspect, 'nodeDocumentFactory', $nodeDocumentFactoryMock);

		$this->assertSame(1, $this->workspaceAspect->publishNodes($joinPoint));
	}
}
?>