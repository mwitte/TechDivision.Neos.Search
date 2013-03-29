<?php

namespace Com\TechDivision\Search\Tests\Unit\Field;


class ResultFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\ResultFactory
	 */
	protected $resultFactory;

	/**
	 * @var \Com\TechDivision\Search\Document\Document
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
	 * @var \Com\TechDivision\Neos\Search\Domain\Model\Result
	 */
	protected $result;

	public function setUp(){
		parent::setUp();
		$this->resultFactory = new \Com\TechDivision\Neos\Search\Factory\ResultFactory();
		$this->document = new \Com\TechDivision\Search\Document\Document();
		$this->configuration = array(
			'Schema' => array(
				'DocumentTypeField' => 'contentType'
			)
		);
		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$nodeResultFactory = $this->getMockBuilder('\Com\TechDivision\Neos\Search\Factory\Result\NodeResultFactory', array('createResultFromNodeDocument'))->disableOriginalConstructor()->getMock();
		$this->result = new \Com\TechDivision\Neos\Search\Domain\Model\Result();
		$nodeResultFactory->expects($this->any())->method('createResultFromNodeDocument')->will($this->returnValue($this->result));
		$this->inject($this->resultFactory, 'nodeResultFactory', $nodeResultFactory);
	}

	public function testCreateFromDocumentWithoutField(){
		$this->assertSame(null, $this->resultFactory->createFromDocument($this->document, $this->workspaceMock, $this->configuration));
	}

	/**
	 * @depends testCreateFromDocumentWithoutField
	 */
	public function testCreateFromDocumentWithFieldWrongValue(){
		$field = new \Com\TechDivision\Search\Field\Field('contentType', 'wrongValue');
		$this->document->addField($field);
		$this->assertSame(null, $this->resultFactory->createFromDocument($this->document, $this->workspaceMock, $this->configuration));
	}

	/**
	 * @depends testCreateFromDocumentWithFieldWrongValue
	 */
	public function testCreateFromDocumentWithValidDocument(){
		$field = new \Com\TechDivision\Search\Field\Field('contentType', 'T3CRNode');
		$this->document->addField($field);
		$this->assertSame($this->result, $this->resultFactory->createFromDocument($this->document, $this->workspaceMock, $this->configuration));
	}

	public function testCreateMultipleWithoutDocuments(){
		$this->assertSame(array(), $this->resultFactory->createMultipleFromDocuments(array(), $this->workspaceMock, $this->configuration));
	}

	/**
	 * @depends testCreateMultipleWithoutDocuments
	 */
	public function testCreateMultipleWithWrongDocument(){
		$documents = array($this->document);
		$this->assertSame(array(), $this->resultFactory->createMultipleFromDocuments($documents, $this->workspaceMock, $this->configuration));
	}

	/**
	 * @depends testCreateMultipleWithoutDocuments
	 */
	public function testCreateMultipleWithValidDocument(){
		$field = new \Com\TechDivision\Search\Field\Field('contentType', 'T3CRNode');
		$this->document->addField($field);
		$documents = array($this->document);
		$this->assertSame(array($this->result), $this->resultFactory->createMultipleFromDocuments($documents, $this->workspaceMock, $this->configuration));
	}
}
?>