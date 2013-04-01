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
	 * @var \TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository
	 * @Flow\Inject
	 */
	protected $workspaceRepository;

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\DocumentFactoryInterface
	 * @Flow\Inject
	 */
	protected $documentFactory;

	/**
	 * @param $token
	 * @return array with \Com\TechDivision\Search\Document\DocumentInterface
	 */
	public function search($token, $rows = 50, $offset = 0){
		$fieldFactory = new \Com\TechDivision\Neos\Search\Factory\FieldFactory();
		$query = $this->searchProvider->searchByString(
			$token,
			$fieldFactory->createFromMultipleConfigurations(),
			$rows,
			$offset);
		return $this->resultFactory->createMultipleFromDocuments(
			$query,
			$this->getWorkspace(),
			$this->settings);
	}

	/**
	 * TODO to automate selective on edit of nodes by AOP
	 */
	public function updateAllDocuments(){
		// only if the provider has an index which can be filled
		if($this->searchProvider->providerNeedsInputDocuments()){
			$documents = $this->documentFactory->getAllDocuments($this->getWorkspace(), $this->settings);
			foreach($documents as $document){
				$this->searchProvider->addDocument($document);
			}
		}
	}

	public function updateDocument(\Com\TechDivision\Search\Document\Document $document){
		// only if the provider has an index which can be filled
		if($this->searchProvider->providerNeedsInputDocuments()){
			$this->searchProvider->addDocument($document);
		}
	}

	public function removeAllDocuments(){
		// only if the provider has an index which can be filled
		if($this->searchProvider->providerNeedsInputDocuments()){
			$documents = $this->documentFactory->getAllDocuments($this->getWorkspace(), $this->settings);
			$docCount = 0;
			/** @var $document \Com\TechDivision\Search\Document\Document */
			foreach($documents as $document){
				$identifierField = $document->getField($this->settings['Schema']['DocumentIdentifierField']);
				if($identifierField){
					$this->searchProvider->removeDocumentByIdentifier($identifierField->getValue());
					$docCount++;
				}
			}
			return $docCount;
		}
	}

	public function providerNeedsInputDocuments(){
		return $this->searchProvider->providerNeedsInputDocuments();
	}

	/**
	 * @return \TYPO3\TYPO3CR\Domain\Model\Workspace|NULL
	 */
	protected function getWorkspace(){
		return $this->workspaceRepository->findByName($this->settings['Workspace'])->getFirst();
	}
}
?>