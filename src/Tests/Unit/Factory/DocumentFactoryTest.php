<?php

namespace Com\TechDivision\Search\Tests\Unit\Field;


class DocumentFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\DocumentFactory
	 */
	protected $documentFactory;

	/**
	 * @var array
	 */
	protected $completeConfiguration;

	public function setUp(){
		parent::setUp();
		$this->documentFactory = new \Com\TechDivision\Neos\Search\Factory\DocumentFactory();

		$this->completeConfiguration = array(
			'DocumentTypeField' => 'cat',
			'DocumentTypes' => array(
				'T3CRNode' => array(
					'MyContentType' => array(
						'properties' => array(
							'text' => array(
								'fieldName' => 'textAlias',
								'fieldValue' => 'value'
							)
						)
					)
				)
			),
			'FieldNames' => array(
				'textAlias' => 'text'
			),
			'DocumentIdentifierField' => 'id'
		);
	}

	private function getNodeMockWithProperty($contentTypeName, $propertyName, $propertyValue, $identifier = null){

		$contentTypeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\ContentType', array("getName"))->disableOriginalConstructor()->getMock();
		$contentTypeMock->expects($this->any())->method("getName")->will($this->returnValue($contentTypeName));

		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array("getProperties", "getProperty", "getContentType", "getIdentifier"))->disableOriginalConstructor()->getMock();
		$nodeMock->expects($this->any())->method("getProperties")->will($this->returnValue(array($propertyName => null)));
		$nodeMock->expects($this->any())->method("getProperty")->will($this->returnValue($propertyValue));
		$nodeMock->expects($this->any())->method("getContentType")->will($this->returnValue($contentTypeMock));
		$nodeMock->expects($this->any())->method("getIdentifier")->will($this->returnValue($identifier));
		return $nodeMock;
	}

	public function testCreateFromNodeWithEmptyConfiguration(){
		$nodeMock = $this->getNodeMockWithProperty('ContentType', 'text', 'value');
		$configuration = array(
			'DocumentTypes' => array(
				'T3CRNode' => array()
			),
			'FieldNames' => array(),
			'DocumentIdentifierField' => 'id'
		);
		$this->assertSame(null, $this->documentFactory->createFromNode($nodeMock, $configuration));
	}

	/**
	 * @depends testCreateFromNodeWithEmptyConfiguration
	 */
	public function testCreateFromNodeWithContentTypeNotConfigured(){
		$nodeMock = $this->getNodeMockWithProperty('ContentType', 'text', 'value');
		$configuration = array(
			'DocumentTypes' => array(
				'T3CRNode' => array(
					'OtherContentType' => null
				)
			),
			'FieldNames' => array(),
			'DocumentIdentifierField' => 'id'
		);
		$this->assertSame(null, $this->documentFactory->createFromNode($nodeMock, $configuration));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeNotConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfiguredEmptyProperties(){
		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'value');
		$configuration = array(
			'DocumentTypes' => array(
				'T3CRNode' => array(
					'MyContentType' => array(
						'properties' => null
					)
				)
			),
			'FieldNames' => array(),
			'DocumentIdentifierField' => 'id'
		);
		$this->assertSame(null, $this->documentFactory->createFromNode($nodeMock, $configuration));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeNotConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfiguredMissingField(){
		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'myValue', 21);

		// remove the matching fieldName
		unset($this->completeConfiguration['FieldNames']['textAlias']);

		$field = new \Com\TechDivision\Search\Field\Field('text', 'myValue');
		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField($field);
		$document->addField(new \Com\TechDivision\Search\Field\Field('id', 21));
		$this->assertEquals(null, $this->documentFactory->createFromNode($nodeMock, $this->completeConfiguration));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeNotConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfigured(){
		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'myValue', 21);

		$field = new \Com\TechDivision\Search\Field\Field('text', 'myValue');
		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField($field);
		$document->addField(new \Com\TechDivision\Search\Field\Field('id', 21));
		$document->addField(new \Com\TechDivision\Search\Field\Field($this->completeConfiguration['DocumentTypeField'], 'T3CRNode'));
		$this->assertEquals($document, $this->documentFactory->createFromNode($nodeMock, $this->completeConfiguration));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeConfiguredMissingField
	 */
	public function testCreateFromNodeWithContentTypeConfiguredWithFieldBoost(){
		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'myValue', 21);
		// modify configuration
		$this->completeConfiguration['DocumentTypes']['T3CRNode']['MyContentType']['documentBoost'] = 1.35;

		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField(new \Com\TechDivision\Search\Field\Field('text', 'myValue'));
		$document->addField(new \Com\TechDivision\Search\Field\Field('id', 21));
		$document->addField(new \Com\TechDivision\Search\Field\Field($this->completeConfiguration['DocumentTypeField'], 'T3CRNode'));
		$document->setBoost(1.35);
		$this->assertEquals($document, $this->documentFactory->createFromNode($nodeMock, $this->completeConfiguration));
	}
}
?>