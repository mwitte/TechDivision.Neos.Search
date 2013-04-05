<?php
namespace TechDivision\Neos\Search\Factory;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;
use TechDivision\Neos\Search\Domain\Model\Result;

/**
 * @Flow\Scope("singleton")
 */
class ResultFactory implements ResultFactoryInterface{

	/**
	 * @var \TechDivision\Neos\Search\Factory\Result\NodeResultFactory
	 * @Flow\Inject
	 */
	protected $nodeResultFactory;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Inject the settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * This Method delegates the creation of responseObjects, you may extend this class and overwrite this method
	 * to add you own results
	 *
	 * @param \TechDivision\Search\Document\DocumentInterface $document
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return \TechDivision\Neos\Search\Domain\Model\Result|null
	 */
	public function createFromDocument(
		\TechDivision\Search\Document\DocumentInterface $document,
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace)
	{

		if($document->getField($this->settings['Schema']['DocumentTypeField'])){
			switch($document->getField($this->settings['Schema']['DocumentTypeField'])->getValue()){
				case 'TYPO3-TYPO3CR-Domain-Model-Node':
					//var_dump($document);
					return $this->nodeResultFactory->createResultFromNodeDocument($document, $workspace);
			}
		}
		return null;
	}

	/**
	 * @param array $documents of type \TechDivision\Search\Document\DocumentInterface
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return array of \TechDivision\Neos\Search\Domain\Model\Result
	 */
	public function createMultipleFromDocuments(
		array $documents,
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace)
	{
		$results = array();
		$pageNodes = array();
		/** @var $document \TechDivision\Search\Document\DocumentInterface */
		foreach($documents as $document){
			$pageNodeIdentifierField = $document->getField($this->settings['Schema']['PageNodeIdentifier']);
			if($pageNodeIdentifierField){
				$pageNodeIdentifier = $pageNodeIdentifierField->getValue();

				// removes page node duplicates
				if(!in_array($pageNodeIdentifier, $pageNodes)){
					$result = $this->createFromDocument($document, $workspace);
					if($result){
						$results[] = $result;
						$pageNodes[] = $pageNodeIdentifier;
					}
				}
			}
		}
		return $results;
	}
}
?>