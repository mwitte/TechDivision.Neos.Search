<?php
namespace TechDivision\Neos\Search\Domain\Model;

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


class Result {

	/**
	 * This node is the lowest node of type TYPO3.Neos.NodeTypes:Page which contains the document's data
	 *
	 * @var \TYPO3\TYPO3CR\Domain\Model\Node
	 */
	protected $pageNode;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\Node
	 */
	protected $node;

	/**
	 * @var \TechDivision\Search\Document\Document
	 */
	protected $document;

	/**
	 * @param \TechDivision\Search\Document\Document $document
	 */
	public function setDocument(\TechDivision\Search\Document\Document$document)
	{
		$this->document = $document;
	}

	/**
	 * @return \TechDivision\Search\Document\Document
	 */
	public function getDocument()
	{
		return $this->document;
	}

	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 */
	public function setPageNode(\TYPO3\TYPO3CR\Domain\Model\Node $node)
	{
		$this->pageNode = $node;
	}

	/**
	 * @return \TYPO3\TYPO3CR\Domain\Model\Node
	 */
	public function getPageNode()
	{
		return $this->pageNode;
	}

	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 */
	public function setNode($node)
	{
		$this->node = $node;
	}

	/**
	 * @return \TYPO3\TYPO3CR\Domain\Model\Node
	 */
	public function getNode()
	{
		return $this->node;
	}
}
?>