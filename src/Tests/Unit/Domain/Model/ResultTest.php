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
	protected $result;

	public function setUp(){
		parent::setUp();
		$this->result = new \Com\TechDivision\Neos\Search\Domain\Model\Result();
	}

	public function testSetGetNode(){
		$nodeMock = $this->getMockBuilder('\TYPO3\TYPO3CR\Domain\Model\Node', array())->disableOriginalConstructor()->getMock();
		$this->result->setNode($nodeMock);
		$this->assertSame($nodeMock, $this->result->getNode($nodeMock));
	}

	public function testSetGetDocument(){
		$document = new \Com\TechDivision\Search\Document\Document();
		$this->result->setDocument($document);
		$this->assertSame($document, $this->result->getDocument());
	}
}
?>