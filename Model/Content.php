<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * Class Content
 * @package Truelab\KottiModelBundle
 *
 * @TypeInfo({
 *   "table" = "contents",
 *   "type"  = "content",
 *   "fields" = {
 *      "default_view","description","language","owner", "state",
 *      "creation_date","modification_date", "in_navigation"
 *   },
 *   "associated_table" = "contents",
 *   "association" = "nodes.id = contents.id"
 * })
 */
class Content extends Node
{
    protected $defaultView;

    protected $description;

    protected $language;

    protected $owner;

    protected $state;

    protected $creationDate;

    protected $modificationDate;

    protected $inNavigation;

    /**
     * @return mixed
     */
    public function getDefaultView()
    {
        return $this->defaultView;
    }

    /**
     * @param mixed $defaultView
     */
    public function setDefaultView($defaultView)
    {
        $this->defaultView = $defaultView;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = new \DateTime($creationDate);
    }

    /**
     * @return mixed
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @param mixed $modificationDate
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = new \DateTime($modificationDate);
    }

    /**
     * @return mixed
     */
    public function getInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * @param mixed $inNavigation
     */
    public function setInNavigation($inNavigation)
    {
        $this->inNavigation = $inNavigation;
    }
}

