<?php
namespace TechDivision\Neos\Search\Factory\Result;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;
use TechDivision\Search\Document\Document;
use TechDivision\Neos\Search\Domain\Model\Result;

/**
 * @Flow\Scope("singleton")
 */
class NodeResultFactory{

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 * @Flow\Inject
	 */
	protected $nodeRepository;

	/**
	 * @var \TechDivision\Neos\Search\Service\NodeService
	 * @Flow\Inject
	 */
	protected $nodeService;

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
	 * @param \TechDivision\Search\Document\Document $document
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return \TechDivision\Neos\Search\Domain\Model\Result|null
	 */
	public function createResultFromNodeDocument(
		Document $document,
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace)
	{
		if($document->getField($this->settings['Schema']['DocumentIdentifierField'])){
			$pageNode = $this->nodeService->getPageNodeByNodeIdentifier($document->getField($this->settings['Schema']['DocumentIdentifierField'])->getValue(), $workspace);
			if($pageNode){
				$result = new Result();
				$result->setPageNode($pageNode);
				$result->setDocument($document);
				$result->setNode($this->nodeRepository->findOneByIdentifier($document->getField($this->settings['Schema']['DocumentIdentifierField'])->getValue(), $workspace));
				return $result;
			}
		}
		return null;
	}
}
?>