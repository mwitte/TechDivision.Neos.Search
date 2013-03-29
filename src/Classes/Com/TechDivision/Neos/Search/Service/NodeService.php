<?php
namespace Com\TechDivision\Neos\Search\Service;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\Node;

class NodeService{

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 */
	protected $nodeRepository;

	public function __construct(\TYPO3\TYPO3CR\Domain\Repository\NodeRepository $nodeRepository){
		$this->nodeRepository = $nodeRepository;
	}

	/**
	 * @param string $nodeId UUid of a node
	 * @param \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace
	 * @return \TYPO3\TYPO3CR\Domain\Model\Node|null
	 */
	public function getPageNodeByNodeIdentifier($nodeId, \TYPO3\TYPO3CR\Domain\Model\Workspace $workspace){
		$node = $this->nodeRepository->findOneByIdentifier($nodeId, $workspace);
		if($node){
			$pageNode = $this->getPageNode($node, $workspace);
			return $pageNode;
		}
		return null;
	}

	/**
	 * Finds recursive the related pageNode
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $node
	 * @return null|\TYPO3\TYPO3CR\Domain\Model\Node
	 */
	public function getPageNode(Node $node, $workspace){
		// TODO configurable, look test also
		if($node->getContentType()->getName() == 'TYPO3.Neos.ContentTypes:Page'){
			return $node;
		}
		$parentNode = $this->nodeRepository->findOneByPath($node->getParentPath(), $workspace);
		if($parentNode){
			return $this->getPageNode($parentNode, $workspace);
		}else{
			return null;
		}
	}
}
?>