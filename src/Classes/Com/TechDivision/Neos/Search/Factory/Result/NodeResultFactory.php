<?php
namespace Com\TechDivision\Neos\Search\Factory\Result;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;
use Com\TechDivision\Search\Document\Document;
use Com\TechDivision\Neos\Search\Domain\Model\Result;

class NodeResultFactory{

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 * @Flow\Inject
	 */
	protected $nodeRepository;

	/**
	 * @var \Com\TechDivision\Neos\Search\Service\NodeService
	 * @Flow\Inject
	 */
	protected $nodeService;

	/**
	 * @param \Com\TechDivision\Search\Document\Document $document
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return \Com\TechDivision\Neos\Search\Domain\Model\Result|null
	 */
	public function createResultFromNodeDocument(
		Document $document,
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace,
		array $configuration)
	{
		if($document->getField($configuration['Schema']['DocumentIdentifierField'])){
			$pageNode = $this->nodeService->getPageNodeByNodeIdentifier($document->getField($configuration['Schema']['DocumentIdentifierField'])->getValue(), $workspace);
			if($pageNode){
				$result = new Result();
				$result->setNode($pageNode);
				$result->setDocument($document);
				return $result;
			}
		}
		return null;
	}
}
?>