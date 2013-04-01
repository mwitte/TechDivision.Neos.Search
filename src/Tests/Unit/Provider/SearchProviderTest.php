<?php

namespace Com\TechDivision\Search\Tests\Unit\Field;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class SearchProviderTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Provider\SearchProvider
	 */
	protected $searchProvider;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $providerMock;

	public function setUp(){
		parent::setUp();
		$this->searchProvider = new \Com\TechDivision\Neos\Search\Provider\SearchProvider();

		$workspaceRepositoryMock = $this->getMock('\TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository', array('findByName'));
		$queryResultMock = $this->getMockBuilder('\TYPO3\Flow\Persistence\Generic\QueryResult', array('getFirst'))->disableOriginalConstructor()->getMock();
		$workspaceMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Workspace')->disableOriginalConstructor()->getMock();
		$queryResultMock->expects($this->any())->method('getFirst')->will($this->returnValue($workspaceMock));
		$workspaceRepositoryMock->expects($this->any())->method('findByName')->will($this->returnValue($queryResultMock));
		$this->inject($this->searchProvider, 'workspaceRepository', $workspaceRepositoryMock);

		$this->providerMock = $this->getMockBuilder('\Com\TechDivision\Search\Provider\ProviderInterface', array('searchByString', 'addDocument', 'providerNeedsInputDocuments'))->getMock();
		$this->providerMock->expects($this->any())->method('searchByString')->will($this->returnValue(array()));
		$this->providerMock->expects($this->any())->method('addDocument')->will($this->returnValue(true));
	}

	private function getDocumentFactoryMock($getAllDocumentsDocs = array()){
		$documentFactoryMock = $this->getMockBuilder('\Com\TechDivision\Neos\Search\Factory\DocumentFactoryInterface', array('getAllDocuments'))->getMock();
		$documentFactoryMock->expects($this->any())->method('getAllDocuments')->will($this->returnValue($getAllDocumentsDocs));
		return $documentFactoryMock;
	}

	public function testSearch(){
		$fieldFactoryMock = $this->getMock('\Com\TechDivision\Neos\Search\Factory\FieldFactory', array('createFromMultipleConfigurations'));
		$fieldFactoryMock->expects($this->any())->method('createFromMultipleConfigurations')->will($this->returnValue(array()));
		$this->inject($this->searchProvider, 'fieldFactory', $fieldFactoryMock);

		$resultFactoryMock = $this->getMockBuilder('\Com\TechDivision\Neos\Search\Factory\ResultFactoryInterface', array('createMultipleFromDocuments'))->getMock();
		$resultFactoryMock->expects($this->any())->method('createMultipleFromDocuments')->will($this->returnValue(array()));
		$this->inject($this->searchProvider, 'resultFactory', $resultFactoryMock);
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->assertSame(array(), $this->searchProvider->search('token'));
	}

	public function testUpdateAllDocumentsProviderNeedsNoInputDocs(){
		// overwrite vom setUp
		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(false));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->assertSame(null, $this->searchProvider->updateAllDocuments());
	}

	/**
	 * @depends testUpdateAllDocumentsProviderNeedsNoInputDocs
	 */
	public function testUpdateAllDocumentsNoDocumentsFound(){
		$this->inject($this->searchProvider, 'documentFactory', $this->getDocumentFactoryMock());

		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(true));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->assertSame(0, $this->searchProvider->updateAllDocuments());
	}

	/**
	 * @depends testUpdateAllDocumentsNoDocumentsFound
	 */
	public function testUpdateAllDocumentsDocumentsFound(){
		$this->inject($this->searchProvider, 'documentFactory', $this->getDocumentFactoryMock(array(new \Com\TechDivision\Search\Document\Document())));

		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(true));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->assertSame(1, $this->searchProvider->updateAllDocuments());
	}

	public function testUpdateDocumentProviderNeedsNoInputDoc(){
		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(false));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->assertSame(null, $this->searchProvider->updateDocument(new \Com\TechDivision\Search\Document\Document()));
	}

	/**
	 * @depends testUpdateDocumentProviderNeedsNoInputDoc
	 */
	public function testUpdateDocument(){
		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(true));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->assertSame(true, $this->searchProvider->updateDocument(new \Com\TechDivision\Search\Document\Document()));
	}

	public function testRemoveAllDocumentsProviderNeedsNoInputDoc(){
		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(false));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->assertSame(null, $this->searchProvider->removeAllDocuments());
	}

	/**
	 * @depends testRemoveAllDocumentsProviderNeedsNoInputDoc
	 */
	public function testRemoveAllDocumentsNoDocumentsFound(){
		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(true));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->inject($this->searchProvider, 'documentFactory', $this->getDocumentFactoryMock());
		$this->assertSame(0, $this->searchProvider->removeAllDocuments());
	}

	/**
	 * @depends testRemoveAllDocumentsNoDocumentsFound
	 */
	public function testRemoveAllDocumentsWithEmptyDocument(){
		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(true));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$this->inject($this->searchProvider, 'documentFactory', $this->getDocumentFactoryMock(array(new \Com\TechDivision\Search\Document\Document())));
		$this->assertSame(0, $this->searchProvider->removeAllDocuments());
	}

	/**
	 * @depends testRemoveAllDocumentsNoDocumentsFound
	 */
	public function testRemoveAllDocumentsWithFilledDocument(){
		$this->providerMock->expects($this->any())->method('providerNeedsInputDocuments')->will($this->returnValue(true));
		$this->inject($this->searchProvider, 'provider', $this->providerMock);
		$document = new \Com\TechDivision\Search\Document\Document();
		$document->addField(new \Com\TechDivision\Search\Field\Field('identifier', '123456'));
		$this->inject($this->searchProvider, 'documentFactory', $this->getDocumentFactoryMock(array($document)));
		$this->inject(
			$this->searchProvider,
			'settings', array(
				'Schema' => array(
					'DocumentIdentifierField' => 'identifier'
				),
				'Workspace' => 'live'
			)
		);
		$this->assertSame(1, $this->searchProvider->removeAllDocuments());
	}
}
?>