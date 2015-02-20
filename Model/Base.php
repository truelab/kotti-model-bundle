<?php

namespace Truelab\KottiModelBundle\Model;
use Truelab\KottiModelBundle\Repository\RepositoryInterface;

/**
 * Class Base
 * @package Truelab\KottiModelBundle
 */
abstract class Base implements \JsonSerializable, \ArrayAccess
{
    protected $id;

    /**
     * FIXME
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * FIXME
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Gets the qualified class name
     * @return string
     */
    public static function getClass()
    {
        return get_called_class();
    }

    //
    //
    // JSON SERIALIZABLE
    //
    //---------------------------------------

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $serialized = [];
        foreach($this as $key => $value) {

            if((is_array($value)
                || is_string($value)
                || is_bool($value)
                || is_numeric($value))) {

                if(is_string($value)) {
                    if(mb_detect_encoding($value, 'UTF-8', true) == 'UTF-8') {
                        $serialized[$key] = $value;
                    }else{
                        $serialized[$key] = base64_encode($value);
                    }
                }else{
                    $serialized[$key] = $value;
                }
            }
        }
        return $serialized;
    }



    //
    //
    // ARRAY ACCESS
    //
    //---------------------------------------

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset) && $this[$offset] !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->{self::getGetMethod($offset)}();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new \RuntimeException();
        }

        $this->{self::getSetMethod($offset)}($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->{self::getSetMethod($offset)}(null);
    }

    //
    //
    // PRIVATE METHODS
    //
    // --------------------------------

    private static function getSetMethod($property)
    {
        return 'set' . ucfirst($property);
    }

    private static function getGetMethod($property)
    {
        return 'get' . ucfirst($property);
    }

}
