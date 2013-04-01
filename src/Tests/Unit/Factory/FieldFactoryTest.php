<?php

namespace Com\TechDivision\Search\Tests\Unit\Field;


class FieldFactoryTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\FieldFactory
	 */
	protected $fieldFactory;

	public function setUp(){
		parent::setUp();
		$this->fieldFactory = new \Com\TechDivision\Neos\Search\Factory\FieldFactory();

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
			array('fieldName' => 'myFieldNameAlias'),
			array('otherAlias' => 'myFieldName'),
			'test')
		);
	}

	/**
	 * @depends testCreateFromEmptyConfiguration
	 */
	public function testCreateFromConfigurationWithFieldName(){
		$field = new \Com\TechDivision\Search\Field\Field('myFieldName', '');
		$this->assertEquals($field, $this->fieldFactory->createFromConfiguration(
			array('fieldName' => 'myFieldNameAlias'),
			array('myFieldNameAlias' => 'myFieldName'),
			'test')
		);
	}

	/**
	 * @adepends testCreateFromConfigurationWithFieldName
	 */
	public function testCreateFromMultipleConfigurations(){
		$fields = array(
			new \Com\TechDivision\Search\Field\Field('myFieldName', ''),
			new \Com\TechDivision\Search\Field\Field('myOtherFieldName', ''),
			new \Com\TechDivision\Search\Field\Field('category', 'T3CRNode'),
			new \Com\TechDivision\Search\Field\Field('id', ''),
			new \Com\TechDivision\Search\Field\Field('pageId', '')
		);
		$configurations = array(
			'Schema' => array(
				'DocumentIdentifierField' => 'id',
				'PageNodeIdentifier' => 'pageId',
				'DocumentTypeField' => 'category',
				'DocumentTypes' => array(
					'T3CRNode' => array(
						'ContentTypes' => array(
							'x' => array(
								'properties' =>
								array(
									'propertyName' => array(
										'fieldName' => 'myFieldNameAlias'
									)
								),
							),
							'y' => array(
								'properties' =>
								array(
									'otherPropertyName' => array(
										'fieldName' => 'myOtherFieldNameAlias'
									)
								)
							)
						)
					)
				),
				'FieldNames' => array(
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
		$field = new \Com\TechDivision\Search\Field\Field('text', 'awesomeValue');
		$this->assertEquals($field, $this->fieldFactory->createFromNode(
			array('fieldName' => 'textAlias'),
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
		$field = new \Com\TechDivision\Search\Field\Field('text', 'awesomeValue', 1.7);
		$this->assertEquals($field, $this->fieldFactory->createFromNode(
			array('fieldName' => 'textAlias', 'fieldBoost' => 1.7),
			array('textAlias' => 'text'),
			'name', $nodeMock)
		);
	}
}
?>