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

class NodeDocumentFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory
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
		$this->nodeDocumentFactory = new \TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory();
		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$this->completeConfiguration = array(
			'Schema' => array(
				'PageNodeIdentifier' => 'subject',
				'DocumentTypeField' => 'cat',
				'DocumentTypes' => array(
					'TYPO3-TYPO3CR-Domain-Model-Node' => array(
						'NodeTypes' => array(
							'MyNodeType' => array(
								'properties' => array(
									'text' => array(
										'fieldAlias' => 'textAlias',
										'fieldValue' => 'value'
									)
								)
							)
						)
					)
				),
				'FieldAliases' => array(
					'textAlias' => 'text'
				),
				'DocumentIdentifierField' => 'id'
			)
		);
	}

	private function getNodeMockWithProperty($NodeTypeName, $propertyName, $propertyValue, $identifier = null){

		$NodeTypeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\NodeType', array("getName"))->disableOriginalConstructor()->getMock();
		$NodeTypeMock->expects($this->any())->method("getName")->will($this->returnValue($NodeTypeName));

		$this->workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();

		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array("getProperties", "getProperty", "getNodeType", "getIdentifier"))->disableOriginalConstructor()->getMock();
		$nodeMock->expects($this->any())->method("getProperties")->will($this->returnValue(array($propertyName => null)));
		$nodeMock->expects($this->any())->method("getProperty")->will($this->returnValue($propertyValue));
		$nodeMock->expects($this->any())->method("getNodeType")->will($this->returnValue($NodeTypeMock));
		$nodeMock->expects($this->any())->method("getIdentifier")->will($this->returnValue($identifier));
		return $nodeMock;
	}

	public function testCreateFromNodeWithEmptyConfiguration(){
		$nodeMock = $this->getNodeMockWithProperty('NodeType', 'text', 'value');
		$nodeMock->getNodeType();
		$configuration = array(
			'Schema' => array(
				'DocumentTypes' => array(
					'TYPO3-TYPO3CR-Domain-Model-Node' => array(
						'NodeTypes' => array()
					)
				),
				'FieldAliases' => array(),
				'DocumentIdentifierField' => 'id'
			)
		);
		$this->inject($this->nodeDocumentFactory, 'settings', $configuration);
		$this->assertSame(null, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithEmptyConfiguration
	 */
	public function testCreateFromNodeWithNodeTypeNotConfigured(){
		$nodeMock = $this->getNodeMockWithProperty('NodeType', 'text', 'value');
		$configuration = array(
			'Schema' => array(
				'DocumentTypes' => array(
					'TYPO3-TYPO3CR-Domain-Model-Node' => array(
						'NodeTypes' => array(
							'OtherNodeType' => null
						)
					)
				),
				'FieldAliases' => array(),
				'DocumentIdentifierField' => 'id'
			)
		);
		$this->inject($this->nodeDocumentFactory, 'settings', $configuration);
		$this->assertSame(null, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithNodeTypeNotConfigured
	 */
	public function testCreateFromNodeWithNodeTypeConfiguredEmptyProperties(){
		$nodeMock = $this->getNodeMockWithProperty('MyNodeType', 'text', 'value');
		$configuration = array(
			'Schema' => array(
				'DocumentTypes' => array(
					'TYPO3-TYPO3CR-Domain-Model-Node' => array(
						'NodeTypes' => array(
							'MyNodeType' => array(
								'properties' => null
							)
						)
					)
				),
				'FieldAliases' => array(),
				'DocumentIdentifierField' => 'id'
			)
		);
		$this->inject($this->nodeDocumentFactory, 'settings', $configuration);
		$this->assertSame(null, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithNodeTypeNotConfigured
	 */
	public function testCreateFromNodeWithNodeTypeConfiguredMissingField(){
		$nodeMock = $this->getNodeMockWithProperty('MyNodeType', 'text', 'myValue', 21);

		// remove the matching fieldName
		unset($this->completeConfiguration['Schema']['FieldAliases']['textAlias']);

		$field = new \TechDivision\Search\Field\Field('text', 'myValue');
		$document = new \TechDivision\Search\Document\Document();
		$document->addField($field);
		$document->addField(new \TechDivision\Search\Field\Field('id', 21));
		$this->inject($this->nodeDocumentFactory, 'settings', $this->completeConfiguration);
		$this->assertEquals(null, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithNodeTypeNotConfigured
	 */
	public function testCreateFromNodeWithNodeTypeConfigured(){
		$nodeServiceMock = $this->getMock('TechDivision\Neos\Search\Service', array('getPageNode'));
		$pageNodeMock = $this->getNodeMockWithProperty('PageNodeMock', 'subject', 'myValue', 21);
		$nodeServiceMock->expects($this->any())->method('getPageNode')->will($this->returnValue($pageNodeMock));

		$this->inject($this->nodeDocumentFactory, 'nodeService', $nodeServiceMock);

		$nodeMock = $this->getNodeMockWithProperty('MyNodeType', 'text', 'myValue', 21);

		$field = new \TechDivision\Search\Field\Field('text', 'myValue');
		$document = new \TechDivision\Search\Document\Document();
		$document->addField($field);
		$document->addField(new \TechDivision\Search\Field\Field('id', 21));
		$document->addField(new \TechDivision\Search\Field\Field($this->completeConfiguration['Schema']['DocumentTypeField'], 'TYPO3-TYPO3CR-Domain-Model-Node'));
		$document->addField(new \TechDivision\Search\Field\Field('subject', 21));
		$this->inject($this->nodeDocumentFactory, 'settings', $this->completeConfiguration);
		$this->assertEquals($document, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithNodeTypeConfiguredMissingField
	 */
	public function testCreateFromNodeWithNodeTypeConfiguredWithFieldBoost(){
		$nodeServiceMock = $this->getMock('TechDivision\Neos\Search\Service', array('getPageNode'));
		$pageNodeMock = $this->getNodeMockWithProperty('PageNodeMock', 'subject', 'myValue', 21);
		$nodeServiceMock->expects($this->any())->method('getPageNode')->will($this->returnValue($pageNodeMock));

		$this->inject($this->nodeDocumentFactory, 'nodeService', $nodeServiceMock);


		$nodeMock = $this->getNodeMockWithProperty('MyNodeType', 'text', 'myValue', 21);
		// modify configuration
		$this->completeConfiguration['Schema']['DocumentTypes']['TYPO3-TYPO3CR-Domain-Model-Node']['NodeTypes']['MyNodeType']['documentBoost'] = 1.35;

		$document = new \TechDivision\Search\Document\Document();
		$document->addField(new \TechDivision\Search\Field\Field('text', 'myValue'));
		$document->addField(new \TechDivision\Search\Field\Field('id', 21));
		$document->addField(new \TechDivision\Search\Field\Field($this->completeConfiguration['Schema']['DocumentTypeField'], 'TYPO3-TYPO3CR-Domain-Model-Node'));
		$document->addField(new \TechDivision\Search\Field\Field('subject', 21));
		$document->setBoost(1.35);
		$this->inject($this->nodeDocumentFactory, 'settings', $this->completeConfiguration);

		$this->assertEquals($document, $this->nodeDocumentFactory->createFromNode($nodeMock, $this->workspaceMock));
	}

	/**
	 * @depends testCreateFromNodeWithNodeTypeConfiguredWithFieldBoost
	 */
	public function testGetAllDocumentsNothingFound(){
		$nodeRepositoryMock = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findAll'))->disableOriginalConstructor()->getMock();
		$nodeRepositoryMock->expects($this->any())->method('findAll')->will($this->returnValue(array()));
		$this->inject($this->nodeDocumentFactory, 'nodeRepository', $nodeRepositoryMock);

		$this->assertSame(array(), $this->nodeDocumentFactory->getAllDocuments($this->workspaceMock, $this->completeConfiguration));
	}

	public function testGetAllDocumentsNodeFound(){
		$nodeRepositoryMock = $this->getMockBuilder('TYPO3\TYPO3CR\Domain\Repository\NodeRepository', array('findAll'))->disableOriginalConstructor()->getMock();
		$nodeMock = $this->getNodeMockWithProperty('MyNodeType', 'text', 'myValue', 21);
		$nodeRepositoryMock->expects($this->any())->method('findAll')->will($this->returnValue(array($nodeMock)));
		$this->inject($this->nodeDocumentFactory, 'nodeRepository', $nodeRepositoryMock);

		$nodeServiceMock = $this->getMock('TechDivision\Neos\Search\Service', array('getPageNode'));
		$pageNodeMock = $this->getNodeMockWithProperty('PageNodeMock', 'subject', 'myValue', 21);
		$nodeServiceMock->expects($this->any())->method('getPageNode')->will($this->returnValue($pageNodeMock));

		$this->inject($this->nodeDocumentFactory, 'nodeService', $nodeServiceMock);

		$document = new \TechDivision\Search\Document\Document();
		$document->addField(new \TechDivision\Search\Field\Field('text', 'myValue'));
		$document->addField(new \TechDivision\Search\Field\Field('id', 21));
		$document->addField(new \TechDivision\Search\Field\Field($this->completeConfiguration['Schema']['DocumentTypeField'], 'TYPO3-TYPO3CR-Domain-Model-Node'));
		$document->addField(new \TechDivision\Search\Field\Field('subject', 21));
		$document->setBoost(1.35);

		// modify configuration
		$this->completeConfiguration['Schema']['DocumentTypes']['TYPO3-TYPO3CR-Domain-Model-Node']['NodeTypes']['MyNodeType']['documentBoost'] = 1.35;
		$this->inject($this->nodeDocumentFactory, 'settings', $this->completeConfiguration);


		$this->assertEquals(array($document), $this->nodeDocumentFactory->getAllDocuments($this->workspaceMock));
	}
}
?>