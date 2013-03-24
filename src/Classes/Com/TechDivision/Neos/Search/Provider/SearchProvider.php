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
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 * @Flow\Inject
	 */
	protected $nodeRepository;

	/**
	 * @param $token
	 * @return array with \Com\TechDivision\Search\Document\DocumentInterface
	 */
	public function search($token, $rows = 50, $offset = 0){
		$fieldFactory = new \Com\TechDivision\Neos\Search\Factory\FieldFactory();
		return $this->searchProvider->searchByString(
			$token,
			$fieldFactory->createFromMultipleConfigurations($this->settings['Schema']['ContentTypes'], $this->settings['Schema']['FieldNames']),
			$rows,
			$offset
		);
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
				$this->settings['Schema']['ContentTypes'],
				$this->settings['Schema']['FieldNames'],
				$this->settings['Schema']['DocumentIdentifierField']
			);
			if($document){
				$this->searchProvider->addDocument($document);
			}
		}

	}
}
?>