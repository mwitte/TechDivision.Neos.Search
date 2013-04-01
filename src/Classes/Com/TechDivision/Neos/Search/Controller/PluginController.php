<?php
namespace Com\TechDivision\Neos\Search\Controller;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

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
	public function indexAction() {
		$request = new \Com\TechDivision\Neos\Search\Domain\Model\Request();
		$this->view->assign('request', $request);
	}

	/**
	 * @param \Com\TechDivision\Neos\Search\Domain\Model\Request $request
	 */
	public function searchAction(\Com\TechDivision\Neos\Search\Domain\Model\Request $request){
		$results = $this->searchProvider->search($request->getToken());
		$this->view->assign('searchResults', $results);
		$this->view->assign('searchRequest', $request);
	}

}

?>