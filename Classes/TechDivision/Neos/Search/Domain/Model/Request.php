<?php
namespace TechDivision\Neos\Search\Domain\Model;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

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