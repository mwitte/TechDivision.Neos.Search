<?php
namespace TechDivision\Neos\Search\Exception;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

/**
 * @codeCoverageIgnore
 */
class UpdatePublishingNodeException extends \Exception {

	public function __construct($message = 'Could not update published node in search index',
								$code = 0,
								\Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
?>