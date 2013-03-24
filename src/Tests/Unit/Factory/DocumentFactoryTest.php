<?php

namespace Com\TechDivision\Search\Tests\Unit\Field;


class DocumentFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\DocumentFactory
	 */
	protected $documentFactory;

	public function setUp(){
		parent::setUp();
		$this->documentFactory = new \Com\TechDivision\Neos\Search\Factory\DocumentFactory();

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
		$this->assertSame(null, $this->documentFactory->createFromNode($nodeMock, array(), array(), ''));
	}

	/**
	 * @depends testCreateFromNodeWithEmptyConfiguration
	 */
	public function testCreateFromNodeWithContentTypeNotConfigured(){
		$nodeMock = $this->getNodeMockWithProperty('ContentType', 'text', 'value');
		$this->assertSame(null, $this->documentFactory->createFromNode($nodeMock, array('OtherContentType' => null), array(), ''));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeNotConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfiguredEmptyProperties(){
		$nodeMock = $this->getNodeMockWithProperty('ContentType', 'text', 'value');
		$configuration = array(
			'ContentType' => array(
				'properties' => null
			)
		);
		$this->assertSame(null, $this->documentFactory->createFromNode($nodeMock, $configuration, array(), ''));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeNotConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfigured(){
		$nodeMock = $this->getNodeMockWithProperty('ContentType', 'text', 'myValue', 21);
		$configuration = array(
			'ContentType' => array(
				'properties' => array(
					'text' => array(
						'fieldName' => 'textAlias',
						'fieldValue' => 'value'
					)
				)
			)
		);
		$field = new \Com\TechDivision\Search\Field\Field('text', 'myValue');
		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField($field);
		$document->addField(new \Com\TechDivision\Search\Field\Field('identifier', 21));
		$this->assertEquals($document, $this->documentFactory->createFromNode($nodeMock, $configuration, array('textAlias' => 'text'), 'identifier'));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfiguredWithFieldBoost(){
		$nodeMock = $this->getNodeMockWithProperty('ContentType', 'text', 'myValue', 21);
		$configuration = array(
			'ContentType' => array(
				'properties' => array(
					'text' => array(
						'fieldName' => 'textAlias',
						'fieldValue' => 'value'
					)
				),
				'documentBoost' => 1.35
			)
		);
		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField(new \Com\TechDivision\Search\Field\Field('text', 'myValue'));
		$document->addField(new \Com\TechDivision\Search\Field\Field('identifier', 21));
		$document->setBoost(1.35);
		$this->assertEquals($document, $this->documentFactory->createFromNode($nodeMock, $configuration, array('textAlias' => 'text'), 'identifier'));
	}
}
?>