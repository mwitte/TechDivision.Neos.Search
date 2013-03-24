<?php
namespace Com\TechDivision\Neos\Search\Factory;

use TYPO3\Flow\Annotations as Flow;
use Com\TechDivision\Search\Document\Document;

class FieldFactory {

	/**
	 * @param string $propertyName
	 * @param array $propertyConfiguration
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 * @return \Com\TechDivision\Search\Field\Field|null
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
	 * @return \Com\TechDivision\Search\Field\Field|null
	 */
	public function createFromConfiguration(array $configuration, array $fieldNames, $propertyName = '', \TYPO3\TYPO3CR\Domain\Model\Node $node = null){
		if(array_key_exists('fieldName', $configuration)){
			if(array_key_exists($configuration['fieldName'], $fieldNames)){
				$fieldName = $fieldNames[$configuration['fieldName']];
				// leave value blank
				if($node){
					$field = new \Com\TechDivision\Search\Field\Field($fieldName,
						$node->getProperty($propertyName));
				}else{
					$field = new \Com\TechDivision\Search\Field\Field($fieldName, '');
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
	 * @param array $contentTypeConfigurations configurations for multiple ContentTypes
	 * @return array with Com\TechDivision\Search\Field\Field
	 */
	public function createFromMultipleConfigurations(array $contentTypeConfigurations, array $fieldNames){
		$fields = array();
		foreach($contentTypeConfigurations as $propertyName => $configuration){

			foreach($configuration['properties'] as $propertyConfiguration){
				$field = $this->createFromConfiguration($propertyConfiguration, $fieldNames, $propertyName);
				if($field){
					$fields[] = $field;
				}

			}

		}
		return $fields;
	}
}

?>