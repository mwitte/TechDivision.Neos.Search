<?php

namespace Com\TechDivision\Search\Tests\Unit\Field;


class DocumentFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\DocumentFactory
	 */
	protected $documentFactory;

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory
	 */
	protected $nodeDocumentFactoryMock;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\Workspace
	 */
	protected $workspaceMock;

	public function setUp(){
		parent::setUp();
		$this->documentFactory = new \Com\TechDivision\Neos\Search\Factory\DocumentFactory();
		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$this->nodeDocumentFactoryMock = $this->getMockBuilder('\Com\TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory', array('getAllDocuments'))->disableOriginalConstructor()->getMock();
		$this->nodeDocumentFactoryMock->expects($this->any())->method('getAllDocuments')->will($this->returnValue(array()));
	}

	public function testGetAllDocuments(){
		$this->inject($this->documentFactory, 'nodeDocumentFactory', $this->nodeDocumentFactoryMock);
		$this->assertSame(array(), $this->documentFactory->getAllDocuments($this->workspaceMock, array()));
	}
}
?>