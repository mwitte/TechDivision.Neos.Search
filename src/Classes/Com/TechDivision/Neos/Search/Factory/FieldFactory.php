<?php
namespace Com\TechDivision\Neos\Search\Factory;

use TYPO3\Flow\Annotations as Flow;

/**
 *
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
	 * @return array with Com\TechDivision\Search\Field\Field
	 */
	public function createFromMultipleConfigurations(){
		$fields = array();
		foreach($this->settings['Schema']['DocumentTypes'] as $documentTypeName => $documentType){
			foreach($documentType['ContentTypes'] as $propertyName => $configuration){
				foreach($configuration['properties'] as $propertyConfiguration){
					$field = $this->createFromConfiguration($propertyConfiguration, $this->settings['Schema']['FieldNames'], $propertyName);
					if($field){
						$fields[] = $field;
					}
				}
			}
			$fields[] = new \Com\TechDivision\Search\Field\Field($this->settings['Schema']['DocumentTypeField'], $documentTypeName);
			$fields[] = new \Com\TechDivision\Search\Field\Field($this->settings['Schema']['DocumentIdentifierField'], '');
			$fields[] = new \Com\TechDivision\Search\Field\Field($this->settings['Schema']['PageNodeIdentifier'], '');
		}

		return $fields;
	}
}

?>