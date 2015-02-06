<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "below_content_box_managers",
 *   "type"  = "below_content_box_manager",
 *   "associated_table" = "base_box_managers",
 *   "association" = "below_content_box_managers.id = base_box_managers.id"
 * })
 */
class BelowContentBoxManager extends BaseBoxManager
{

}
