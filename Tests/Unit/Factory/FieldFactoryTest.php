<?php

namespace TechDivision\Search\Tests\Unit\Field;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class FieldFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TechDivision\Neos\Search\Factory\FieldFactory
	 */
	protected $fieldFactory;

	public function setUp(){
		parent::setUp();
		$this->fieldFactory = new \TechDivision\Neos\Search\Factory\FieldFactory();

	}

	private function getNodeMockWithProperty($name, $value){
		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array("getProperties", "getProperty"))->disableOriginalConstructor()->getMock();
		$nodeMock->expects($this->any())->method("getProperties")->will($this->returnValue(array($name => null)));
		$nodeMock->expects($this->any())->method("getProperty")->will($this->returnValue($value));
		return $nodeMock;
	}


	public function testCreateFromEmptyConfiguration(){
		$this->assertSame(null, $this->fieldFactory->createFromConfiguration(array(), array(), 'test'));
	}

	/**
	 * @depends testCreateFromEmptyConfiguration
	 */
	public function testCreateFromConfigurationWithFieldNameNotMatchingFieldnames(){
		$this->assertEquals(null, $this->fieldFactory->createFromConfiguration(
			array('fieldAlias' => 'myFieldNameAlias'),
			array('otherAlias' => 'myFieldName'),
			'test')
		);
	}

	/**
	 * @depends testCreateFromEmptyConfiguration
	 */
	public function testCreateFromConfigurationWithFieldName(){
		$field = new \TechDivision\Search\Field\Field('myFieldName', '');
		$this->assertEquals($field, $this->fieldFactory->createFromConfiguration(
			array('fieldAlias' => 'myFieldNameAlias'),
			array('myFieldNameAlias' => 'myFieldName'),
			'test')
		);
	}

	/**
	 * @adepends testCreateFromConfigurationWithFieldName
	 */
	public function testCreateFromMultipleConfigurations(){
		$fields = array(
			new \TechDivision\Search\Field\Field('myFieldName', ''),
			new \TechDivision\Search\Field\Field('myOtherFieldName', ''),
			new \TechDivision\Search\Field\Field('category', 'TYPO3-TYPO3CR-Domain-Model-Node'),
			new \TechDivision\Search\Field\Field('id', ''),
			new \TechDivision\Search\Field\Field('pageId', '')
		);
		$configurations = array(
			'Schema' => array(
				'DocumentIdentifierField' => 'id',
				'PageNodeIdentifier' => 'pageId',
				'DocumentTypeField' => 'category',
				'DocumentTypes' => array(
					'TYPO3-TYPO3CR-Domain-Model-Node' => array(
						'ContentTypes' => array(
							'x' => array(
								'properties' =>
								array(
									'propertyName' => array(
										'fieldAlias' => 'myFieldNameAlias'
									)
								),
							),
							'y' => array(
								'properties' =>
								array(
									'otherPropertyName' => array(
										'fieldAlias' => 'myOtherFieldNameAlias'
									)
								)
							)
						)
					)
				),
				'FieldAliases' => array(
					'myFieldNameAlias' => 'myFieldName',
					'myOtherFieldNameAlias' => 'myOtherFieldName'
				)
			)
		);

		$this->inject($this->fieldFactory, 'settings', $configurations);
		$this->assertEquals($fields, $this->fieldFactory->createFromMultipleConfigurations());
	}

	public function testCreateFromNodeWithWrongName(){
		$nodeMock = $this->getNodeMockWithProperty('name', 'value');
		$this->assertSame(null, $this->fieldFactory->createFromNode(array(), array(), 'otherName', $nodeMock));
	}

	/**
	 * @depends testCreateFromNodeWithWrongName
	 */
	public function testCreateFromNodeWithEmptyConfiguration(){
		$nodeMock = $this->getNodeMockWithProperty('name', 'value');
		$this->assertSame(null, $this->fieldFactory->createFromNode(array(), array(), 'name', $nodeMock));
	}

	/**
	 * @depends testCreateFromNodeWithEmptyConfiguration
	 */
	public function testCreateFromNodeWithConfiguration(){
		$nodeMock = $this->getNodeMockWithProperty('name', 'awesomeValue');
		$field = new \TechDivision\Search\Field\Field('text', 'awesomeValue');
		$this->assertEquals($field, $this->fieldFactory->createFromNode(
			array('fieldAlias' => 'textAlias'),
			array('textAlias' => 'text'),
			'name',
			$nodeMock)
		);
	}

	/**
	 * @depends testCreateFromNodeWithEmptyConfiguration
	 */
	public function testCreateFromNodeWithConfigurationAndBoost(){
		$nodeMock = $this->getNodeMockWithProperty('name', 'awesomeValue');
		$field = new \TechDivision\Search\Field\Field('text', 'awesomeValue', 1.7);
		$this->assertEquals($field, $this->fieldFactory->createFromNode(
			array('fieldAlias' => 'textAlias', 'fieldBoost' => 1.7),
			array('textAlias' => 'text'),
			'name', $nodeMock)
		);
	}
}
?>