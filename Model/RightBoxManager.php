<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "right_box_managers",
 *   "type"  = "right_box_manager",
 *   "associated_table" = "base_box_managers",
 *   "association" = "right_box_managers.id = base_box_managers.id"
 * })
 */
class RightBoxManager extends BaseBoxManager
{

}
