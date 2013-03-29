<?php
namespace Com\TechDivision\Neos\Search\Factory;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;
use Com\TechDivision\Search\Document\Document;
use Com\TechDivision\Neos\Search\Domain\Model\Result;

class ResultFactory implements ResultFactoryInterface{

	/**
	 * @var \Com\TechDivision\Neos\Search\Factory\Result\NodeResultFactory
	 * @Flow\Inject
	 */
	protected $nodeResultFactory;

	/**
	 * This Method delegates the creation of responseObjects, you may extend this class and overwrite this method
	 * to add you own results
	 *
	 * @param \Com\TechDivision\Search\Document\DocumentInterface $document
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return \Com\TechDivision\Neos\Search\Domain\Model\Result|null
	 */
	public function createFromDocument(
		\Com\TechDivision\Search\Document\DocumentInterface $document,
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace,
		array $configuration)
	{

		if($document->getField($configuration['Schema']['DocumentTypeField'])){
			switch($document->getField($configuration['Schema']['DocumentTypeField'])->getValue()){
				case 'T3CRNode':
					//var_dump($document);
					return $this->nodeResultFactory->createResultFromNodeDocument($document, $workspace, $configuration);
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
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace,
		array $configuration)
	{
		$results = array();
		/** @var $document \Com\TechDivision\Search\Document\DocumentInterface */
		foreach($documents as $document){
			$result = $this->createFromDocument($document, $workspace, $configuration);
			if($result){
				$results[] = $result;
			}
		}
		return $results;
	}
}
?>