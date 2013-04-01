<?php

namespace Com\TechDivision\Search\Tests\Unit\Field;


class NodeDocumentFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory
	 */
	protected $nodeDocumentFactory;

	/**
	 * @var array
	 */
	protected $completeConfiguration;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\Workspace
	 */
	protected $workspaceMock;

	public function setUp(){
		parent::setUp();
		$this->nodeDocumentFactory = new \Com\TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory();
		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$this->completeConfiguration = array(
			'Schema' => array(
				'PageNodeIdentifier' => 'subject',
				'DocumentTypeField' => 'cat',
				'DocumentTypes' => array(
					'T3CRNode' => array(
						'ContentTypes' => array(
							'MyContentType' => array(
								'properties' => array(
									'text' => array(
										'fieldName' => 'textAlias',
										'fieldValue' => 'value'
									)
								)
							)
						)
					)
				),
				'FieldNames' => array(
					'textAlias' => 'text'
				),
				'DocumentIdentifierField' => 'id'
			)
		);
	}

	private function getNodeMockWithProperty($contentTypeName, $propertyName, $propertyValue, $identifier = null){

		$contentTypeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\ContentType', array("getName"))->disableOriginalConstructor()->getMock();
		$contentTypeMock->expects($this->any())->method("getName")->will($this->returnValue($contentTypeName));

		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

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
			'Schema' => array(
				'DocumentTypes' => array(
					'T3CRNode' => array(
						'ContentTypes' => array()
					)
				),
				'FieldNames' => array(),
				'DocumentIdentifierField' => 'id'
			)
		);
		$this->inject($this->nodeDocumentFactory, 'settings', $configuration);
		$this->assertSame(null, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithEmptyConfiguration
	 */
	public function testCreateFromNodeWithContentTypeNotConfigured(){
		$nodeMock = $this->getNodeMockWithProperty('ContentType', 'text', 'value');
		$configuration = array(
			'Schema' => array(
				'DocumentTypes' => array(
					'T3CRNode' => array(
						'ContentTypes' => array(
							'OtherContentType' => null
						)
					)
				),
				'FieldNames' => array(),
				'DocumentIdentifierField' => 'id'
			)
		);
		$this->inject($this->nodeDocumentFactory, 'settings', $configuration);
		$this->assertSame(null, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeNotConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfiguredEmptyProperties(){
		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'value');
		$configuration = array(
			'Schema' => array(
				'DocumentTypes' => array(
					'T3CRNode' => array(
						'ContentTypes' => array(
							'MyContentType' => array(
								'properties' => null
							)
						)
					)
				),
				'FieldNames' => array(),
				'DocumentIdentifierField' => 'id'
			)
		);
		$this->inject($this->nodeDocumentFactory, 'settings', $configuration);
		$this->assertSame(null, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeNotConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfiguredMissingField(){
		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'myValue', 21);

		// remove the matching fieldName
		unset($this->completeConfiguration['Schema']['FieldNames']['textAlias']);

		$field = new \Com\TechDivision\Search\Field\Field('text', 'myValue');
		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField($field);
		$document->addField(new \Com\TechDivision\Search\Field\Field('id', 21));
		$this->inject($this->nodeDocumentFactory, 'settings', $this->completeConfiguration);
		$this->assertEquals(null, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeNotConfigured
	 */
	public function testCreateFromNodeWithContentTypeConfigured(){
		$nodeServiceMock = $this->getMock('Com\TechDivision\Neos\Search\Service', array('getPageNode'));
		$pageNodeMock = $this->getNodeMockWithProperty('PageNodeMock', 'subject', 'myValue', 21);
		$nodeServiceMock->expects($this->any())->method('getPageNode')->will($this->returnValue($pageNodeMock));

		$this->inject($this->nodeDocumentFactory, 'nodeService', $nodeServiceMock);

		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'myValue', 21);

		$field = new \Com\TechDivision\Search\Field\Field('text', 'myValue');
		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField($field);
		$document->addField(new \Com\TechDivision\Search\Field\Field('id', 21));
		$document->addField(new \Com\TechDivision\Search\Field\Field($this->completeConfiguration['Schema']['DocumentTypeField'], 'T3CRNode'));
		$document->addField(new \Com\TechDivision\Search\Field\Field('subject', 21));
		$this->inject($this->nodeDocumentFactory, 'settings', $this->completeConfiguration);
		$this->assertEquals($document, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeConfiguredMissingField
	 */
	public function testCreateFromNodeWithContentTypeConfiguredWithFieldBoost(){
		$nodeServiceMock = $this->getMock('Com\TechDivision\Neos\Search\Service', array('getPageNode'));
		$pageNodeMock = $this->getNodeMockWithProperty('PageNodeMock', 'subject', 'myValue', 21);
		$nodeServiceMock->expects($this->any())->method('getPageNode')->will($this->returnValue($pageNodeMock));

		$this->inject($this->nodeDocumentFactory, 'nodeService', $nodeServiceMock);


		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'myValue', 21);
		// modify configuration
		$this->completeConfiguration['Schema']['DocumentTypes']['T3CRNode']['ContentTypes']['MyContentType']['documentBoost'] = 1.35;

		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField(new \Com\TechDivision\Search\Field\Field('text', 'myValue'));
		$document->addField(new \Com\TechDivision\Search\Field\Field('id', 21));
		$document->addField(new \Com\TechDivision\Search\Field\Field($this->completeConfiguration['Schema']['DocumentTypeField'], 'T3CRNode'));
		$document->addField(new \Com\TechDivision\Search\Field\Field('subject', 21));
		$document->setBoost(1.35);
		$this->inject($this->nodeDocumentFactory, 'settings', $this->completeConfiguration);

		$this->assertEquals($document, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithContentTypeConfiguredWithFieldBoost
	 */
	public function testGetAllDocumentsNothingFound(){
		$nodeRepositoryMock = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findAll'))->disableOriginalConstructor()->getMock();
		$nodeRepositoryMock->expects($this->any())->method('findAll')->will($this->returnValue(array()));
		$this->inject($this->nodeDocumentFactory, 'nodeRepository', $nodeRepositoryMock);

		$this->assertSame(array(), $this->nodeDocumentFactory->getAllDocuments($this->workspaceMock, $this->completeConfiguration));
	}

	public function testGetAllDocumentsNodeFound(){
		$nodeRepositoryMock = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findAll'))->disableOriginalConstructor()->getMock();
		$nodeMock = $this->getNodeMockWithProperty('MyContentType', 'text', 'myValue', 21);
		$nodeRepositoryMock->expects($this->any())->method('findAll')->will($this->returnValue(array($nodeMock)));
		$this->inject($this->nodeDocumentFactory, 'nodeRepository', $nodeRepositoryMock);

		$nodeServiceMock = $this->getMock('Com\TechDivision\Neos\Search\Service', array('getPageNode'));
		$pageNodeMock = $this->getNodeMockWithProperty('PageNodeMock', 'subject', 'myValue', 21);
		$nodeServiceMock->expects($this->any())->method('getPageNode')->will($this->returnValue($pageNodeMock));

		$this->inject($this->nodeDocumentFactory, 'nodeService', $nodeServiceMock);

		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField(new \Com\TechDivision\Search\Field\Field('text', 'myValue'));
		$document->addField(new \Com\TechDivision\Search\Field\Field('id', 21));
		$document->addField(new \Com\TechDivision\Search\Field\Field($this->completeConfiguration['Schema']['DocumentTypeField'], 'T3CRNode'));
		$document->addField(new \Com\TechDivision\Search\Field\Field('subject', 21));
		$document->setBoost(1.35);

		// modify configuration
		$this->completeConfiguration['Schema']['DocumentTypes']['T3CRNode']['ContentTypes']['MyContentType']['documentBoost'] = 1.35;
		$this->inject($this->nodeDocumentFactory, 'settings', $this->completeConfiguration);


		$this->assertEquals(array($document), $this->nodeDocumentFactory->getAllDocuments($this->workspaceMock));
	}
}
?>