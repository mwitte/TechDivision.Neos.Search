<?php

namespace Com\TechDivision\Neos\Search\Tests\Functional\Factory;


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