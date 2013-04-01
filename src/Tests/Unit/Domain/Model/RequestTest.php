<?php
namespace Com\TechDivision\Neos\Search\Tests\Unit\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search".*
 *                                                                        *
 *                                                                        */

/**
 * Testcase for Result
 */
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