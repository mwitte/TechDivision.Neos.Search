<?php
namespace Com\TechDivision\Neos\Search\Exception;

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