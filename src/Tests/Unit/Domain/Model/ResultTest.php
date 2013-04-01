<?php
namespace Com\TechDivision\Neos\Search\Tests\Unit\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search".*
 *                                                                        *
 *                                                                        */

/**
 * Testcase for Result
 */
class ResultTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \Com\TechDivision\Neos\Search\Domain\Model\Result
	 */
	protected $request;

	public function setUp(){
		parent::setUp();
		$this->request = new \Com\TechDivision\Neos\Search\Domain\Model\Result();
	}

	public function testSetGetPageNode(){
		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array())->disableOriginalConstructor()->getMock();
		$this->request->setPageNode($nodeMock);
		$this->assertSame($nodeMock, $this->request->getPageNode($nodeMock));
	}

	public function testSetGetDocument(){
		$document = new \Com\TechDivision\Search\Document\Document();
		$this->request->setDocument($document);
		$this->assertSame($document, $this->request->getDocument());
	}

	public function testSetGetNode(){
		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array())->disableOriginalConstructor()->getMock();
		$this->request->setNode($nodeMock);
		$this->assertSame($nodeMock, $this->request->getNode());
	}
}
?>