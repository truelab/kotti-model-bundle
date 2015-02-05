<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "documents",
 *   "type"  = "document",
 *   "fields" = { "body", "mime_type" },
 *   "associated_table" = "contents",
 *   "association" = "documents.id = contents.id"
 * })
 */
class Document extends Content
{
    protected $body;

    protected $mimeType;

    public function getType()
    {
        return 'documents';
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }
}
