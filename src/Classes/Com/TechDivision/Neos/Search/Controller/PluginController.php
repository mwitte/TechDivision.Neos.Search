<?php
namespace Com\TechDivision\Neos\Search\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Plugin controller for the Com.TechDivision.Neos.Search package 
 *
 * @Flow\Scope("singleton")
 */
class PluginController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \Com\TechDivision\Neos\Search\Provider\SearchProvider
	 * @Flow\Inject
	 */
	protected $searchProvider;

	/**
	 * @return void
	 */
	public function showAction() {
		$request = new \Com\TechDivision\Neos\Search\Domain\Model\Request();
		$this->view->assign('request', $request);
	}

	/**
	 * @param \Com\TechDivision\Neos\Search\Domain\Model\Request $request
	 */
	public function searchAction(\Com\TechDivision\Neos\Search\Domain\Model\Request $request){
		$results = $this->searchProvider->search($request->getToken());
		$this->view->assign('results', $results);
	}

}

?>