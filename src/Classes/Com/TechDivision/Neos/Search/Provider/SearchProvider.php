<?php
namespace Com\TechDivision\Neos\Search\Provider;

use TYPO3\Flow\Annotations as Flow;

/**
 * This is my great class.
 *
 * @Flow\Scope("singleton")
 */
class SearchProvider {

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
	 * @var \Com\TechDivision\Search\Provider\ProviderInterface
	 * @Flow\Inject
	 */
	protected $searchProvider;

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\ResultFactoryInterface
	 * @Flow\Inject
	 */
	protected $resultFactory;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 * @Flow\Inject
	 */
	protected $nodeRepository;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository
	 * @Flow\Inject
	 */
	protected $workspaceRepository;

	/**
	 * @param $token
	 * @return array with \Com\TechDivision\Search\Document\DocumentInterface
	 */
	public function search($token, $rows = 50, $offset = 0){
		$fieldFactory = new \Com\TechDivision\Neos\Search\Factory\FieldFactory();
		$query = $this->searchProvider->searchByString(
			$token,
			$fieldFactory->createFromMultipleConfigurations($this->settings['Schema'], $this->settings['Schema']['FieldNames']),
			$rows,
			$offset);
		return $this->resultFactory->createMultipleFromDocuments(
			$query,
			$this->workspaceRepository->findByName('live')->getFirst(),
			$this->settings);
	}

	/**
	 * TODO to automate selective on edit of nodes by AOP
	 */
	public function updateAllDocuments(){
		$nodes = $this->nodeRepository->findAll();
		$documentFactory = new \Com\TechDivision\Neos\Search\Factory\DocumentFactory();
		foreach($nodes as $node){
			$document = $documentFactory->createFromNode(
				$node,
				$this->settings['Schema']
			);
			if($document){
				$this->searchProvider->addDocument($document);
			}
		}



	}
}
?>