<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "base_box_managers",
 *   "type"  = "base_box_manager",
 *   "associated_table" = "contents",
 *   "association" = "base_box_managers.id = contents.id"
 * })
 */
class BaseBoxManager extends Content
{

}
