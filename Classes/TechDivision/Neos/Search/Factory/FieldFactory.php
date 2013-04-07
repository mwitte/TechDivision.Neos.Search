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

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class FieldFactory {

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
	 * @param string $propertyName
	 * @param array $propertyConfiguration
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 * @return \TechDivision\Search\Field\Field|null
	 */
	public function createFromNode(array $propertyConfiguration, array $fieldNames, $propertyName, \TYPO3\TYPO3CR\Domain\Model\Node $node){
		// only if the given node contains the configured property
		if(array_key_exists($propertyName, $node->getProperties())){
			return $this->createFromConfiguration($propertyConfiguration, $fieldNames, $propertyName, $node);
		}
		return null;
	}

	/**
	 * @param string $propertyName
	 * @param array $configuration
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 * @return \TechDivision\Search\Field\Field|null
	 */
	public function createFromConfiguration(array $configuration, array $fieldNames, $propertyName = '', \TYPO3\TYPO3CR\Domain\Model\Node $node = null){
		if(array_key_exists('fieldAlias', $configuration)){
			if(array_key_exists($configuration['fieldAlias'], $fieldNames)){
				$fieldName = $fieldNames[$configuration['fieldAlias']];
				// leave value blank
				if($node){
					$field = new \TechDivision\Search\Field\Field($fieldName,
						$node->getProperty($propertyName));
				}else{
					$field = new \TechDivision\Search\Field\Field($fieldName, '');
				}
				if(array_key_exists('fieldBoost', $configuration)){
					$field->setBoost(floatval($configuration['fieldBoost']));
				}
				return $field;
			}
		}
		return null;
	}

	/**
	 * @return array with TechDivision\Search\Field\Field
	 */
	public function createFromMultipleConfigurations(){
		$fields = array();
		foreach($this->settings['Schema']['DocumentTypes'] as $documentTypeName => $documentType){
			foreach($documentType['NodeTypes'] as $propertyName => $configuration){
				foreach($configuration['properties'] as $propertyConfiguration){
					$field = $this->createFromConfiguration($propertyConfiguration, $this->settings['Schema']['FieldAliases'], $propertyName);
					if($field){
						$fields[] = $field;
					}
				}
			}
			$fields[] = new \TechDivision\Search\Field\Field($this->settings['Schema']['DocumentTypeField'], $documentTypeName);
			$fields[] = new \TechDivision\Search\Field\Field($this->settings['Schema']['DocumentIdentifierField'], '');
			$fields[] = new \TechDivision\Search\Field\Field($this->settings['Schema']['PageNodeIdentifier'], '');
		}

		return $fields;
	}
}

?>