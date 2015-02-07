<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "nodes",
 *   "type"  = NULL,
 *   "fields" = {"type", "parent_id","path","position","_acl", "name","title", "annotations"},
 * })
 */
class Node extends Base implements NodeInterface
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
        return $this->acl;
    }

    /**
     * @param mixed $acl
     */
    public function setAcl($acl)
    {
        $this->acl = json_decode($acl);
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

        if($this->_repository) {

            if(is_array($this->children)) {

                if($class) {
                    return array_filter($this->children, function ($child) use($class) {
                        return get_class($child) === $class;
                    });
                }

                return $this->children;
            }

            $mergedCriteria = array_merge(
                [(!$class ? 'WHERE '  :  ' ' ) .'nodes.parent_id = ? ' => $this->getId()],
                $criteria
            );


            $this->children = $this->_repository->findAll($class, $mergedCriteria, $orderBy, $limit, $offset);

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
            'contents.in_navigation = ?' => true
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

    public function getParent()
    {
        // TODO: Implement getParent() method.
    }

}
