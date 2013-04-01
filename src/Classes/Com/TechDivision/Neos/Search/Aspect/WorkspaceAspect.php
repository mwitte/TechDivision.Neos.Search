<?php
namespace Com\TechDivision\Neos\Search\Aspect;

use TYPO3\Flow\Annotations as Flow;

/**
 *
 * @Flow\Aspect
 */
class WorkspaceAspect{

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository
	 * @Flow\Inject
	 */
	protected $workspaceRepository;

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\Document\NodeDocumentFactory
	 * @Flow\Inject
	 */
	protected $nodeDocumentFactory;

	/**
	 * @var \Com\TechDivision\Neos\Search\Provider\SearchProvider
	 * @Flow\Inject
	 */
	protected $searchProvider;

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
	 * This method gets called when a node gets published
	 *
	 * @Flow\After("method(TYPO3\TYPO3CR\Domain\Model\Workspace->publishNodes())")
	 * @param \TYPO3\Flow\AOP\JoinPointInterface $joinPoint
	 * @throws \Com\TechDivision\Neos\Search\Exception\UpdatePublishingNodeException
	 * @return int|null
	 */
	public function publishNodes(\TYPO3\Flow\AOP\JoinPointInterface $joinPoint){
		// only if the provider needs to update it's index
		if($this->searchProvider->providerNeedsInputDocuments()){
			try {
				// only if the target workspace is same like the configured
				if($joinPoint->getMethodArgument('targetWorkspaceName') === $this->settings['Workspace']){
					// get the workspace
					$workspace = $this->workspaceRepository->findByName($this->settings['Workspace'])->getFirst();
					/** @var $nodes array<\TYPO3\TYPO3CR\Domain\Model\NodeInterface> */
					$nodes = $joinPoint->getMethodArgument('nodes');
					$updatedNodes = 0;
					// for each node
					foreach($nodes as $node){
						// create a document from the node
						$document = $this->nodeDocumentFactory->createFromNode($node, $workspace);
						if($document){
							// update document at searchProvider
							$this->searchProvider->updateDocument($document);
							$updatedNodes++;
						}
					}
					return $updatedNodes;
				}
			}catch (\Exception $e){
				throw new \Com\TechDivision\Neos\Search\Exception\UpdatePublishingNodeException();
			}
		}
		return null;
	}
}
?>