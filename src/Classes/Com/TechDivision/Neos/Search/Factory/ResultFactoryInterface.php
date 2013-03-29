<?php

namespace Com\TechDivision\Neos\Search\Factory;

interface ResultFactoryInterface
{
	/**
	 * @param \Com\TechDivision\Search\Document\DocumentInterface $document
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return \Com\TechDivision\Neos\Search\Domain\Model\Result|null
	 */
	public function createFromDocument(
		\Com\TechDivision\Search\Document\DocumentInterface $document,
		\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace,
		array $configuration);

	/**
	 * @param array $documents of type \Com\TechDivision\Search\Document\DocumentInterface
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return array
	 */
	/**
	 * @param array $documents of type \Com\TechDivision\Search\Document\DocumentInterface
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @param array $configuration
	 * @return array
	 */
	public function createMultipleFromDocuments(
			array $documents,
			\TYPO3\TYPO3CR\Domain\Model\Workspace $workspace,
			array $configuration);
}
