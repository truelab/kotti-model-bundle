<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "left_box_managers",
 *   "type"  = "left_box_manager",
 *   "associated_table" = "base_box_managers",
 *   "association" = "left_box_managers.id = base_box_managers.id"
 * })
 */
class LeftBoxManager extends BaseBoxManager
{

}
