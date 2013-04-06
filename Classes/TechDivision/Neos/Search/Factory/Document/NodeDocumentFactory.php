<?php
namespace TechDivision\Neos\Search\Factory\Document;

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
use TechDivision\Search\Document\Document;

/**
 *
 * @Flow\Scope("singleton")
 */
class NodeDocumentFactory implements \TechDivision\Neos\Search\Factory\DocumentFactoryInterface{


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
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 * @param array $configuration
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return \TechDivision\Search\Document\Document|null
	 */
	public function createFromNode(\TYPO3\TYPO3CR\Domain\Model\Node $node, \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace){
		$document = new Document();
		// only if the node is configured
		if(array_key_exists($node->getNodeType()->getName(), $this->settings['Schema']['DocumentTypes']['TYPO3-TYPO3CR-Domain-Model-Node']['NodeTypes'])){
			$typeConfiguration = $this->settings['Schema']['DocumentTypes']['TYPO3-TYPO3CR-Domain-Model-Node']['NodeTypes'][$node->getNodeType()->getName()];
			if(array_key_exists('properties', $typeConfiguration) && is_array($typeConfiguration['properties'])){
				$fieldFactory = new \TechDivision\Neos\Search\Factory\FieldFactory();
				// iterate over properties
				foreach($typeConfiguration['properties'] as $propertyName => $propertyConfiguration){
					$field = $fieldFactory->createFromNode($propertyConfiguration, $this->settings['Schema']['FieldAliases'], $propertyName, $node);
					if($field){
						$document->addField($field);
					}
				}
				if(array_key_exists('documentBoost', $typeConfiguration)){
					$document->setBoost(floatval($typeConfiguration['documentBoost']));
				}
			}
		}
		// return document only if at least one field got added
		if($document->getFieldCount() > 0){
			// add the unique identifier to the document
			$document->addField(new \TechDivision\Search\Field\Field($this->settings['Schema']['DocumentIdentifierField'], $node->getIdentifier()));
			$document->addField(new \TechDivision\Search\Field\Field($this->settings['Schema']['DocumentTypeField'], 'TYPO3-TYPO3CR-Domain-Model-Node'));
			//$document->addField(new \TechDivision\Search\Field\Field($configuration['DocumentTypes']['TYPO3-TYPO3CR-Domain-Model-Node']['NodeTypeField'], $node->getNodeType()->getName()));
			$document->addField(new \TechDivision\Search\Field\Field($this->settings['Schema']['PageNodeIdentifier'], $this->nodeService->getPageNode($node, $workspace)->getIdentifier()));
			//var_dump($document);
			return $document;
		}
		return null;
	}


	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return array TechDivision\Search\Document\Document
	 */
	public function getAllDocuments(\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace)
	{
		$documents = array();
		$nodes = $this->nodeRepository->findAll();
		foreach($nodes as $node){
			$document = $this->createFromNode(
				$node,
				$workspace
			);
			if($document){
				$documents[] = $document;
			}
		}
		return $documents;
	}
}

?>