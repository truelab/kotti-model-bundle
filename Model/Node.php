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
            return $this->_repository->findAll(null, array_merge([
                ['WHERE nodes.parent_id = ? ' => $this->getId()]
            ], $criteria), $orderBy, $limit, $offset);
        }else{
            return [];
        }
    }

    public function getParent()
    {
        // TODO: Implement getParent() method.
    }
}
