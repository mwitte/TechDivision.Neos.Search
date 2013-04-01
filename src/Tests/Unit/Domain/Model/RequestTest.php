<?php
namespace Com\TechDivision\Neos\Search\Tests\Unit\Domain\Model;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

class RequestTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Domain\Model\Request
	 */
	protected $request;

	public function setUp(){
		parent::setUp();
		$this->request = new \Com\TechDivision\Neos\Search\Domain\Model\Request();
	}

	public function testSetGetToken(){
		$this->request->setToken('myToken');
		$this->assertSame('myToken', $this->request->getToken());
	}
}
?>