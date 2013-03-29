<?php
namespace Com\TechDivision\Neos\Search\Domain\Model;

use TYPO3\Flow\Annotations as Flow;


class Result {

	/**
	 * This node is the lowest node of type TYPO3.Neos.ContentTypes:Page which contains the document's data
	 *
	 * @var \TYPO3\TYPO3CR\Domain\Model\Node
	 */
	protected $node;

	/**
	 * @var \Com\TechDivision\Search\Document\Document
	 */
	protected $document;

	/**
	 * @param \Com\TechDivision\Search\Document\Document $document
	 */
	public function setDocument(\Com\TechDivision\Search\Document\Document$document)
	{
		$this->document = $document;
	}

	/**
	 * @return \Com\TechDivision\Search\Document\Document
	 */
	public function getDocument()
	{
		return $this->document;
	}

	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 */
	public function setNode(\TYPO3\TYPO3CR\Domain\Model\Node $node)
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