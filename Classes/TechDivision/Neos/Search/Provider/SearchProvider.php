<?php
namespace TechDivision\Neos\Search\Provider;

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

/**
 * @Flow\Scope("singleton")
 */
class SearchProvider {

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
	 * @var \TechDivision\Search\Provider\ProviderInterface
	 * @Flow\Inject
	 */
	protected $provider;

	/**
	 * @var \TechDivision\Neos\Search\Factory\ResultFactoryInterface
	 * @Flow\Inject
	 */
	protected $resultFactory;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository
	 * @Flow\Inject
	 */
	protected $workspaceRepository;

	/**
	 * @var \TechDivision\Neos\Search\Factory\DocumentFactoryInterface
	 * @Flow\Inject
	 */
	protected $documentFactory;

	/**
	 * @var \TechDivision\Neos\Search\Factory\FieldFactory
	 * @Flow\Inject
	 */
	protected $fieldFactory;

	/**
	 * @param $token
	 * @return array with \TechDivision\Search\Document\DocumentInterface
	 */
	public function search($token, $rows = 50, $offset = 0){
		$query = $this->provider->searchByString(
			$token,
			$this->fieldFactory->createFromMultipleConfigurations(),
			$rows,
			$offset);
		return $this->resultFactory->createMultipleFromDocuments(
			$query,
			$this->getWorkspace(),
			$this->settings);
	}

	/**
	 * TODO to automate selective on edit of nodes by AOP
	 */
	public function updateAllDocuments(){
		// only if the provider has an index which can be filled
		if($this->provider->providerNeedsInputDocuments()){
			$documents = $this->documentFactory->getAllDocuments($this->getWorkspace(), $this->settings);
			$amountUpdated = 0;
			foreach($documents as $document){
				if($this->provider->addDocument($document)){
					$amountUpdated++;
				}
			}
			return $amountUpdated;
		}
		return null;
	}

	public function updateDocument(\TechDivision\Search\Document\Document $document){
		// only if the provider has an index which can be filled
		if($this->provider->providerNeedsInputDocuments()){
			return $this->provider->addDocument($document);
		}
	}

	public function removeAllDocuments(){
		// only if the provider has an index which can be filled
		if($this->provider->providerNeedsInputDocuments()){
			$documents = $this->documentFactory->getAllDocuments($this->getWorkspace(), $this->settings);
			$docCount = 0;
			/** @var $document \TechDivision\Search\Document\Document */
			foreach($documents as $document){
				$identifierField = $document->getField($this->settings['Schema']['DocumentIdentifierField']);
				if($identifierField){
					$this->provider->removeDocumentByField(
						new \TechDivision\Search\Field\Field($identifierField->getName(), $identifierField->getValue())
					);
					$docCount++;
				}
			}
			return $docCount;
		}
		return null;
	}

	/**
	 * @return \TYPO3\TYPO3CR\Domain\Model\Workspace|NULL
	 */
	protected function getWorkspace(){
		return $this->workspaceRepository->findByName($this->settings['Workspace'])->getFirst();
	}

	/**
	 * @return bool
	 */
	public function providerNeedsInputDocuments(){
		return $this->provider->providerNeedsInputDocuments();
	}
}
?>