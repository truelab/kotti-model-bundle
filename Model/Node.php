<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\Repository\RepositoryInterface;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "nodes",
 *   "type"  = NULL,
 *   "fields" = {"type", "parent_id","path","position","_acl", "name","title", "annotations"},
 * })
 */
class Node extends Base implements NodeActiveInterface
{
    protected $type;

    protected $parentId;

    protected $path;

    protected $position;

    protected $acl;

    protected $name;

    protected $title;

    protected $annotations;

    protected $children;

    protected $parent;

    // FIXME
    /** @var  RepositoryInterface  */
    protected $repository;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parentId = $parent_id;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getAcl()
    {
        if(is_array($this->acl)) {
            return $this->acl;
        }else{
            $this->acl = json_decode($this->acl);
        }
        return $this->acl;
    }

    /**
     * @param mixed $acl
     */
    public function setAcl($acl)
    {
        $this->acl = $acl;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * @param mixed $annotations
     */
    public function setAnnotations($annotations)
    {
        $this->annotations = $annotations;
    }

    public function getChildren($class = null, array $criteria = array(), $orderBy = null, $limit = null, $offset = null)
    {

        if($this->repository) {

            $mergedCriteria = array_merge(
                ['nodes.parent_id = ? ' => $this->getId()],
                $criteria
            );

            $this->children = $this->repository->findAll($class, $mergedCriteria, $orderBy, $limit, $offset);

            return $this->children;

        }else{
            return [];
        }
    }

    public function hasInNavigationChildren()
    {
        return count($this->getInNavigationChildren()) > 0;
    }

    public function getInNavigationChildren()
    {
        return $this->getChildren(null, [
            'contents.in_navigation = ?' => true,
            'contents.state = ?' => 'public' // FIXME
        ]);
    }

    public function hasChildren($class = null, array $criteria = array())
    {
        if(!$this->children) {
            return count($this->getChildren()) > 0;
        }else{
            return count($this->children) > 0;
        }
    }

    public function hasParent($class = null, array $criteria = array())
    {
        if(!$this->parent) {
            return  $this->getParent() != null;
        }else{
            return  $this->parent != null;
        }
    }


    public function getParent()
    {
        if(!$this->parentId) {
            return null;
        }

        return $this->repository->find(null, $this->parentId);
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function equals(NodeInterface $node)
    {
        return get_class($this) === get_class($node) && $this->getPath() === $node->getPath();
    }

    /**
     * @param null $class
     * @param array $criteria
     *
     * @return NodeInterface[]
     */
    public function getSiblings($class = null, array $criteria = [])
    {
        return $this->repository->findAll($class, array_merge([
            'nodes.parent_id = ?' => $this->getParentId(),
            'contents.state = ?' => 'public' // FIXME
        ], $criteria));
    }

    /**
     * @param null $class
     * @param array $criteria
     *
     * @return bool
     */
    public function isLeaf($class = null, array $criteria = [])
    {
        return $this->hasChildren($class, $criteria) === false;
    }
}
