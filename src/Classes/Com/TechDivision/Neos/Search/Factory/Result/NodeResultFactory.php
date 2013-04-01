<?php
namespace Com\TechDivision\Neos\Search\Factory\Result;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;
use Com\TechDivision\Search\Document\Document;
use Com\TechDivision\Neos\Search\Domain\Model\Result;

/**
 *
 * @Flow\Scope("singleton")
 */
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
	 * @param \Com\TechDivision\Search\Document\Document $document
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return \Com\TechDivision\Neos\Search\Domain\Model\Result|null
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