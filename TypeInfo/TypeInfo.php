<?php

namespace Truelab\KottiModelBundle\TypeInfo;


/**
 * @Annotation
 */
class TypeInfo
{
    protected $options;

    private $fields;

    public function __construct($options)
    {
        if(isset($options['value'])) {
            $this->options = $options['value'];
        }

        if($this->options) {
            $this->createFields();
            $this->createAlias();
        }
    }

    public function setClass($class)
    {
        $this->options['class'] = $class;
    }

    public function getClass()
    {
        return isset($this->options['class']) ? $this->options['class'] : null;
    }

    public function getTable()
    {
        return isset($this->options['table']) ? $this->options['table'] : null;
    }


    public function getAssociatedTable() {
        return isset($this->options['associated_table']) ? $this->options['associated_table'] : null;
    }

    public function getType()
    {
        return isset($this->options['type']) ? $this->options['type'] : null;
    }

    public function getAssociation()
    {
        return isset($this->options['association']) ? $this->options['association'] : null;
    }

    public function getAlias()
    {
        return isset($this->options['alias']) ? $this->options['alias'] : null;
    }

    /**
     * @return TypeInfoField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function getFieldByAlias($alias)
    {
        foreach($this->getFields() as $field) {
            if($field->getAlias() === $alias) {
                return $field;
            };
        }

        return null;
    }

    public function getOptions()
    {
        return $this->options;
    }

    private function createFields()
    {
        $fieldsArray = isset($this->options['fields']) ? $this->options['fields'] : [];
        $fieldsCollection = [];

        $fieldsCollection[] = new TypeInfoField($this->getTable(), 'id');

        foreach($fieldsArray as $field) {
            $fieldsCollection[] = new TypeInfoField($this->getTable(), $field);
        }

        $this->fields = $fieldsCollection;
    }

    private function createAlias()
    {
        if(!isset($this->options['alias'])) {
            $this->options['alias'] = $this->getType();
        }
    }

}
