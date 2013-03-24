<?php
namespace Com\TechDivision\Neos\Search\Factory;

use TYPO3\Flow\Annotations as Flow;
use Com\TechDivision\Search\Document\Document;

class DocumentFactory {

	public function createFromNode(\TYPO3\TYPO3CR\Domain\Model\Node $node, array $contentTypesConfiguration, array $fieldNames, $identifier){
		$document = new Document();
		// only if the node is configured
		if(array_key_exists($node->getContentType()->getName(), $contentTypesConfiguration)){
			$typeConfiguration = $contentTypesConfiguration[$node->getContentType()->getName()];
			if(array_key_exists('properties', $typeConfiguration) && is_array($typeConfiguration['properties'])){
				$fieldFactory = new FieldFactory();
				// iterate over properties
				foreach($typeConfiguration['properties'] as $propertyName => $propertyConfiguration){
					$document->addField($fieldFactory->createFromNode($propertyConfiguration, $fieldNames, $propertyName, $node));
				}
				if(array_key_exists('documentBoost', $typeConfiguration)){
					$document->setBoost(floatval($typeConfiguration['documentBoost']));
				}
			}
		}
		// return document only if at least one field got added
		if($document->getFieldCount() > 0){
			// add the unique identifier to the document
			$document->addField(new \Com\TechDivision\Search\Field\Field($identifier, $node->getIdentifier()));
			return $document;
		}
		return null;
	}
}

?>