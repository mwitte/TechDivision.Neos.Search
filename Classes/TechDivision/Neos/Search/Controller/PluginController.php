<?php
namespace TechDivision\Neos\Search\Controller;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

use TYPO3\Flow\Annotations as Flow;

/**
 * Plugin controller for the TechDivision.Neos.Search package
 *
 * @Flow\Scope("singleton")
 */
class PluginController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \TechDivision\Neos\Search\Provider\SearchProvider
	 * @Flow\Inject
	 */
	protected $searchProvider;

	/**
	 * @return void
	 */
	public function indexAction() {
		$request = new \TechDivision\Neos\Search\Domain\Model\Request();
		$this->view->assign('request', $request);
	}

	/**
	 * @param \TechDivision\Neos\Search\Domain\Model\Request $request
	 */
	public function searchAction(\TechDivision\Neos\Search\Domain\Model\Request $request){
		$results = $this->searchProvider->search($request->getToken());
		$this->view->assign('results', $results);
		$this->view->assign('request', $request);
	}

}

?>