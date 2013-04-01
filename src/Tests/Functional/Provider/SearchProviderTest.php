<?php

namespace Com\TechDivision\Neos\Search\Tests\Functional\Provider;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class SearchProviderTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = FALSE;

	/**
	 * @var \Com\TechDivision\Neos\Search\Provider\SearchProvider
	 */
	protected $provider;

	public function setUp(){
		parent::setUp();
		$this->provider = $this->objectManager->get('\Com\TechDivision\Neos\Search\Provider\SearchProvider');
	}

	public function testSearchWithoutResult(){
		$this->assertSame(array(), $this->provider->search('unFindAbleUn1queString'));
	}
}
?>