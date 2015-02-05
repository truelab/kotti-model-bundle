<?php

namespace Truelab\KottiModelBundle\Model;

/**
 * Class Base
 * @package Truelab\KottiModelBundle
 */
abstract class Base implements \JsonSerializable, \ArrayAccess
{
    protected $id;

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
        foreach($this as $key => $property) {
            $serialized[$key] = $property;
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
