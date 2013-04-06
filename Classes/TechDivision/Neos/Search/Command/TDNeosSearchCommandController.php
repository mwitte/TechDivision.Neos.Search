<?php
namespace TechDivision\Neos\Search\Command;

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
 * UpdateNodesCommand command controller for the Com.TechDivision.Neos.Search package
 *
 * @Flow\Scope("singleton")
 *
 * No coverage needed, this controller is needed only for development
 * @codeCoverageIgnore
 */
class TDNeosSearchCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @var \TechDivision\Neos\Search\Provider\SearchProvider
	 * @Flow\Inject
	 */
	protected $searchProvider;

	/**
	 * Updates all configured Nodes in DB
	 *
	 * @return void
	 */
	public function updateAllNodesCommand() {
		$this->outputLine('Updated nodes from DB: %s', array($this->searchProvider->updateAllDocuments()));
	}

	/**
	 * Removes all configured Nodes from searchIndex
	 *
	 * @return void
	 */
	public function removeAllDocumentsCommand(){
		$this->outputLine('Removed Docs: %s', array($this->searchProvider->removeAllDocuments()));
	}

	/**
	 * Search with token
	 *
	 * @return void
	 */
	public function searchCommand() {

		if(!$this->request->getExceedingArguments()){
			$this->outputLine('Please provide a token to search with.');
			return;
		}

		$searchToken = "";
		foreach($this->request->getExceedingArguments() as $word){
			$searchToken .= " " . $word;
		}

		$this->outputLine('Searching for "%s"', array($searchToken));
		$results = $this->searchProvider->search($searchToken);
		/** @var $result \TechDivision\Neos\Search\Domain\Model\Result */
		foreach($results as $result){
			$this->outputLine('Page: %s', array($result->getPageNode()->getProperty('title')));
			$document = $result->getDocument();
			$fields = $document->getFields();
			/** @var $field \TechDivision\Search\Field\FieldInterface */
			foreach($fields as $field){
				$this->outputLine('%s: %s', array($field->getName(), $field->getValue()));
			}
			$this->outputLine('');
			$this->outputLine('');
		}
		$this->outputLine('%s document(s) found', array(count($results)));
	}

}

?>