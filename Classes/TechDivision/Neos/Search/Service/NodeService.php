<?php
namespace TechDivision\Neos\Search\Service;

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

/**
 * @Flow\Scope("singleton")
 */
class NodeService{

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 * @Flow\Inject
	 */
	protected $nodeRepository;

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
	 * Gets the PageNode for the given nodeIdentifier. If the given nodeIdentifier corresponds to a PageNode, this
	 * Node will returned
	 *
	 * @param string $nodeId UUid of a node
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return \TYPO3\TYPO3CR\Domain\Model\Node|null
	 */
	public function getPageNodeByNodeIdentifier($nodeId, \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace){
		$node = $this->nodeRepository->findOneByIdentifier($nodeId, $workspace);
		if($node && $this->checkValidity($node)){
			$pageNode = $this->getPageNode($node, $workspace);
			if($pageNode && $this->checkValidity($pageNode)){
				return $pageNode;
			}
		}
		return null;
	}

	/**
	 * Checks if the given Node should be visible in this context
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 * @param string $FLOW_SAPITYPE
	 * @return bool
	 */
	public function checkValidity(\TYPO3\TYPO3CR\Domain\Model\Node $node, $FLOW_SAPITYPE = FLOW_SAPITYPE){
		if($FLOW_SAPITYPE === 'CLI'){
			return true;
		}
		if($node->isAccessible() && $node->isVisible()){
			return true;
		}
		return false;
	}

	/**
	 * Finds recursive the related PageNode, if the given node is a PageNode, this node will returned
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 * @return null|\TYPO3\TYPO3CR\Domain\Model\Node
	 */
	public function getPageNode(Node $node, $workspace){
		if($node->getNodeType()->getName() == $this->settings['ResultNodeType']){
			return $node;
		}
		$parentNode = $this->nodeRepository->findOneByPath($node->getParentPath(), $workspace);
		if($parentNode){
			return $this->getPageNode($parentNode, $workspace);
		}else{
			return null;
		}
	}
}
?>