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
 *
 * @Flow\Scope("singleton")
 *
 * coverage is not needed, only TYPO3 Flow functionality here, should be covered by TYPO3 Flow
 * @codeCoverageIgnore
 */
class BackendController extends \TYPO3\Neos\Controller\Module\StandardController {


	/**
	 * @var \TechDivision\Neos\Search\Provider\SearchProvider
	 * @Flow\Inject
	 */
	protected $searchProvider;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('providerNeedsInputDocs', $this->searchProvider->providerNeedsInputDocuments());
	}

	/**
	 * Updates all configured Nodes in DB
	 *
	 * @return void
	 */
	public function updateAllDocumentsAction() {
		$this->searchProvider->updateAllDocuments();
		$this->addFlashMessage('Updated all documents');
		$this->redirect('index');
	}

	/**
	 * Removes all configured Nodes from searchIndex
	 *
	 * @return void
	 */
	public function removeAllDocumentsAction(){
		$this->searchProvider->removeAllDocuments();
		$this->addFlashMessage('Removed all documents');
		$this->redirect('index');
	}
}
?>