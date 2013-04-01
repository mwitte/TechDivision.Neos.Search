<?php
namespace Com\TechDivision\Neos\Search\Domain\Model;

use TYPO3\Flow\Annotations as Flow;

/**
 * A Request
 *
 */
class Request {

	/**
	 * The token
	 * @var string
	 */
	protected $token;


	/**
	 * Get the Request's token
	 *
	 * @return string The Request's token
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * Sets this Request's token
	 *
	 * @param string $token The Request's token
	 * @return void
	 */
	public function setToken($token) {
		$this->token = $token;
	}

}
?>