<?php
namespace Com\TechDivision\Neos\Search\ViewHelpers;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "Com.TechDivision.Neos.Search"  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("prototype")
 */
class SearchResultViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Disable the escaping interceptor because otherwise the child nodes would be escaped before this view helper
	 * can decode the text's entities.
	 *
	 * @var boolean
	 */
	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Inject the settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Escapes special characters with their escaped counterparts as needed using PHPs strip_tags() function.
	 *
	 * @param string $value string to format
	 * @param string $token
	 * @return mixed
	 * @see http://www.php.net/manual/function.strip-tags.php
	 * @api
	 */
	public function render($value = NULL, $token = NULL) {
		if ($value === NULL) {
			$value = $this->renderChildren();
		}
		if (!is_string($value)) {
			return $value;
		}
		$value = strip_tags($value);
		if($token){
			return preg_replace(
				"/\w*?".preg_quote($token)."\w*/i",
				$this->settings['SearchResult']['Highlight']['prefix'] . "$0" . $this->settings['SearchResult']['Highlight']['suffix'],
				$value);
		}else{
			return $value;
		}
	}
}

?>