<?php

namespace Com\TechDivision\Search\Tests\Unit\Field;


class NodeServiceTest extends \TYPO3\Flow\Tests\UnitTestCase {

	public function setUp(){
		parent::setUp();


	}

	private function getNodeServiceWithEmptyNodeRepository(){
		$nodeRepositoryMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findOneByIdentifier'))->disableOriginalConstructor()->getMock();
		$nodeRepositoryMock->expects($this->any())->method('findOneByIdentifier')->will($this->returnValue(null));
		$nodeService = new \Com\TechDivision\Neos\Search\Service\NodeService($nodeRepositoryMock);
		return $nodeService;
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
	private function getNodeWithParent($parentNode, $contentTypeName){
		$node = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array('getContentType', 'getParent'))->disableOriginalConstructor()->getMock();
		$node->expects($this->any())->method('getContentType')->will($this->returnValue($this->getContentTypeMock($contentTypeName)));
		$node->expects($this->any())->method('getParent')->will($this->returnValue($parentNode));
		return $node;
	}

	public function testGetPageNodeWithWrongNodeWithoutParent(){
		$nodeService = $this->getNodeServiceWithEmptyNodeRepository();
		$this->assertSame(null, $nodeService->getPageNode($this->getSingleNode('wrongContentTypeName')));
	}

	public function testGetPageNodeWithCorrectNode(){
		$nodeService = $this->getNodeServiceWithEmptyNodeRepository();
		// TODO Configurable, look implementation also
		$node = $this->getSingleNode('TYPO3.Neos.ContentTypes:Page');
		$this->assertSame($node, $nodeService->getPageNode($node));
	}

	public function testGetPageNodeWithWrongNodeWithWrongParent(){
		$parentNode = $this->getSingleNode('wrongContentTypeName');
		$childNode = $this->getNodeWithParent($parentNode, 'wrongContentTypeName');
		$nodeService = $this->getNodeServiceWithEmptyNodeRepository();
		$this->assertSame(null, $nodeService->getPageNode($childNode));
	}

	public function testGetPageNodeWithWrongNodeWithCorrectParent(){
		// TODO Configurable, look implementation also
		$parentNode = $this->getSingleNode('TYPO3.Neos.ContentTypes:Page');
		$childNode = $this->getNodeWithParent($parentNode, 'wrongContentTypeName');
		$nodeService = $this->getNodeServiceWithEmptyNodeRepository();
		$this->assertSame($parentNode, $nodeService->getPageNode($childNode));
	}

	public function testGetPageNodeWithWrongNodeWithWrongParentWithCorrectParent(){
		// TODO Configurable, look implementation also
		$parentParentNode = $this->getSingleNode('TYPO3.Neos.ContentTypes:Page');
		$parentNode = $this->getNodeWithParent($parentParentNode, 'wrongContentTypeName');
		$childNode = $this->getNodeWithParent($parentNode, 'wrongContentTypeName');
		$nodeService = $this->getNodeServiceWithEmptyNodeRepository();
		$this->assertSame($parentParentNode, $nodeService->getPageNode($childNode));
	}

	public function testGetPageNodeByIdentifierNotFound(){
		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$nodeService = $this->getNodeServiceWithEmptyNodeRepository();
		$this->assertSame(null, $nodeService->getPageNodeByNodeIdentifier('identifier', $workspaceMock));
	}

	public function testGetPageNodeByIdentifierFound(){
		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$nodeRepositoryMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findOneByIdentifier'))->disableOriginalConstructor()->getMock();
		// TODO Configurable, look implementation also
		$nodeMock = $this->getSingleNode('TYPO3.Neos.ContentTypes:Page');
		$nodeRepositoryMock->expects($this->any())->method('findOneByIdentifier')->will($this->returnValue($nodeMock));
		$nodeService = new \Com\TechDivision\Neos\Search\Service\NodeService($nodeRepositoryMock);
		$this->assertSame($nodeMock, $nodeService->getPageNodeByNodeIdentifier('someIdentifier', $workspaceMock));
	}
}
?>