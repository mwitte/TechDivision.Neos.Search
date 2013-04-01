<?php
namespace Com\TechDivision\Neos\Search\Factory;

use TYPO3\Flow\Annotations as Flow;
use Com\TechDivision\Search\Document\Document;

/**
 *
 * @Flow\Scope("singleton")
 */
class DocumentFactory implements DocumentFactoryInterface{

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory
	 * @Flow\Inject
	 */
	protected $nodeDocumentFactory;

	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return array Com\TechDivision\Search\Document\Document
	 */
	public function getAllDocuments(\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace)
	{
		$documents = array();
		$documents = array_merge($documents, $this->nodeDocumentFactory->getAllDocuments($workspace));
		return $documents;
	}
}

?>