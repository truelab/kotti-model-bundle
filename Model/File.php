<?php

namespace Truelab\KottiModelBundle\Model;

use Closure;
use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "files",
 *   "type"  = "file",
 *   "fields" = {
 *      "data" : {
 *         "name" : "data",
 *         "lazy" : true
 *      },
 *      "filename",
 *      "mimetype",
 *      "size"
 *   },
 *   "associated_table" = "contents",
 *   "association" = "files.id = contents.id"
 * })
 */
class File extends Content
{

    protected $data;

    protected $dataReference;

    protected $filename;

    protected $mimetype;

    protected $size;

    /**
     * @return mixed
     */
    public function getData()
    {

        if(empty($this->data) && is_callable($this->dataReference)) {
            $reference = $this->dataReference;
            $this->setData($reference());
        }

        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function setDataReference(Closure $dataReference)
    {
        $this->dataReference = $dataReference;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param mixed $mimetype
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }
}
