<?php

namespace TechDivision\Neos\Search\Factory;

/*                                                                        *
 * This belongs to the TYPO3 Flow package "TechDivision.Neos.Search"      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * Copyright (C) 2013 Matthias Witte                                      *
 * http://www.matthias-witte.net                                          */

interface DocumentFactoryInterface
{
	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return array TechDivision\Search\Document\Document
	 */
	public function getAllDocuments(\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace);
}
