<?php

namespace Com\TechDivision\Neos\Search\Factory;

interface DocumentFactoryInterface
{

	/**
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return array Com\TechDivision\Search\Document\Document
	 */
	public function getAllDocuments(\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace);
}
