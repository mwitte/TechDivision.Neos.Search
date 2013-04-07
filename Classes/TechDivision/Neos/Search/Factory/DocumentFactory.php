<?php
namespace TechDivision\Neos\Search\Factory;

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

/**
 * @Flow\Scope("singleton")
 */
class DocumentFactory implements DocumentFactoryInterface{

	/**
	 * @var \TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory
	 * @Flow\Inject
	 */
	protected $nodeDocumentFactory;

	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return array TechDivision\Search\Document\Document
	 */
	public function getAllDocuments(\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace)
	{
		$documents = array();
		$documents = array_merge($documents, $this->nodeDocumentFactory->getAllDocuments($workspace));
		return $documents;
	}
}

?>