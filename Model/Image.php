<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "images",
 *   "type"  = "image",
 *   "fields" = { },
 *   "associated_table" = "files",
 *   "association" = "images.id = files.id"
 * })
 */
class Image extends File
{

}
