<?php

namespace Truelab\KottiModelBundle\Model;

/**
 * Interface ContentInterface
 *
 * @package Truelab\KottiORMBundle\Model
 */
interface ContentInterface extends NodeInterface
{
    const STATE_PUBLIC = 'public';
    const STATE_PRIVATE = 'private';

    /**
     * @return string
     */
    public function getDefaultView();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getLanguage();

    /**
     * @return string
     */
    public function getState();

    /**
     * @return \DateTime
     */
    public function getCreationDate();

    /**
     * @return \DateTime
     */
    public function getModificationDate();

    /**
     * @return string
     */
    public function getOwner();

    /**
     * @return boolean
     */
    public function isInNavigation();

    /**
     * @return boolean
     */
    public function isPrivate();

    /**
     * @return boolean
     */
    public function isPublic();
}
