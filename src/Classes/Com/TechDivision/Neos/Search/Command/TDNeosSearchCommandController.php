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
	 * Search with token
	 *
	 * @param string $token Token to search with
	 * @return void
	 */
	public function searchCommand($token) {
		if(!$token){
			$this->outputLine('Please provide a token to search with.');
			return;
		}
		$this->outputLine('Searching for "%s"', array($token));
		$results = $this->searchProvider->search($token);
		$this->outputLine('Found %s documents:', array(count($results)));
		/** @var $document \Com\TechDivision\Search\Document\DocumentInterface */
		foreach($results as $document){
			$fields = $document->getFields();
			/** @var $field \Com\TechDivision\Search\Field\FieldInterface */
			foreach($fields as $field){
				$this->outputLine('%s: %s', array($field->getName(), $field->getValue()));
			}
			$this->outputLine('');
			$this->outputLine('');
		}
	}

}

?>