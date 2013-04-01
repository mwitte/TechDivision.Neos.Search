<?php
namespace Com\TechDivision\Neos\Search\Factory;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;
use Com\TechDivision\Search\Document\Document;
use Com\TechDivision\Neos\Search\Domain\Model\Result;

/**
 *
 * @Flow\Scope("singleton")
 */
class ResultFactory implements ResultFactoryInterface{

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\Result\NodeResultFactory
	 * @Flow\Inject
	 */
	protected $nodeResultFactory;

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
	 * This Method delegates the creation of responseObjects, you may extend this class and overwrite this method
	 * to add you own results
	 *
	 * @param \Com\TechDivision\Search\Document\DocumentInterface $document
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return \Com\TechDivision\Neos\Search\Domain\Model\Result|null
	 */
	public function createFromDocument(
		\Com\TechDivision\Search\Document\DocumentInterface $document,
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace)
	{

		if($document->getField($this->settings['Schema']['DocumentTypeField'])){
			switch($document->getField($this->settings['Schema']['DocumentTypeField'])->getValue()){
				case 'T3CRNode':
					//var_dump($document);
					return $this->nodeResultFactory->createResultFromNodeDocument($document, $workspace);
			}
		}
		return null;
	}

	/**
	 * @param array $documents of type \Com\TechDivision\Search\Document\DocumentInterface
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return array of \Com\TechDivision\Neos\Search\Domain\Model\Result
	 */
	public function createMultipleFromDocuments(
		array $documents,
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace)
	{
		$results = array();
		$pageNodes = array();
		/** @var $document \Com\TechDivision\Search\Document\DocumentInterface */
		foreach($documents as $document){
			$pageNodeIdentifierField = $document->getField($this->settings['Schema']['PageNodeIdentifier']);
			if($pageNodeIdentifierField){
				$pageNodeIdentifier = $pageNodeIdentifierField->getValue();

				// removes page node duplicates
				if(!in_array($pageNodeIdentifier, $pageNodes)){
					$result = $this->createFromDocument($document, $workspace);
					if($result){
						$results[] = $result;
						$pageNodes[] = $pageNodeIdentifier;
					}
				}
			}
		}
		return $results;
	}
}
?>