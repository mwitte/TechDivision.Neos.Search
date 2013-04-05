<?php

namespace TechDivision\Neos\Search\Tests\Functional\Factory;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class ResultFactoryTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = TRUE;

	public function setUp(){
		parent::setUp();
	}

	public function testIncomplete(){
		$this->markTestIncomplete();
	}
}
?>