<?php

namespace Com\TechDivision\Neos\Search\Tests\Functional\Provider;
/**
 * Testcase for Board
 */
class SearchProviderTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = TRUE;

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

	public function testUpdateAllDocuments(){
		$this->assertEquals(null, $this->provider->updateAllDocuments());
	}
}
?>