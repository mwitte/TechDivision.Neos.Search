<?php
namespace Com\TechDivision\Neos\Search\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * UpdateNodesCommand command controller for the Com.TechDivision.Neos.Search package
 *
 * @Flow\Scope("singleton")
 */
class TDNeosSearchCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @var \Com\TechDivision\Neos\Search\Provider\SearchProvider
	 * @Flow\Inject
	 */
	protected $searchProvider;

	/**
	 * Updates all configured Nodes in DB
	 *
	 * @return void
	 */
	public function updateAllNodesCommand() {
		$this->outputLine('Updated nodes from DB: %s', array($this->searchProvider->updateAllDocuments()));
	}

	/**
	 * Removes all configured Nodes from searchIndex
	 *
	 * @return void
	 */
	public function removeAllDocumentsCommand(){
		$this->outputLine('Removed Docs: %s', array($this->searchProvider->removeAllDocuments()));
	}

	/**
	 * Search with token
	 *
	 * @return void
	 */
	public function searchCommand() {

		if(!$this->request->getExceedingArguments()){
			$this->outputLine('Please provide a token to search with.');
			return;
		}

		$searchToken = "";
		foreach($this->request->getExceedingArguments() as $word){
			$searchToken .= " " . $word;
		}

		$this->outputLine('Searching for "%s"', array($searchToken));
		$results = $this->searchProvider->search($searchToken);
		$this->outputLine('Found %s documents:', array(count($results)));
		/** @var $result \Com\TechDivision\Neos\Search\Domain\Model\Result */
		foreach($results as $result){
			$this->outputLine('Page: %s', array($result->getPageNode()->getProperty('title')));
			$document = $result->getDocument();
			$fields = $document->getFields();
			/** @var $field \Com\TechDivision\Search\Field\FieldInterface */
			foreach($fields as $field){
				$this->outputLine('%s: %s', array($field->getName(), $field->getValue()));
			}
			$this->outputLine('');
			$this->outputLine('');
		}
		$this->outputLine('Found %s documents:', array(count($results)));
	}

}

?>