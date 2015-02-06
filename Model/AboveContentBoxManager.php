<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "above_content_box_managers",
 *   "type"  = "above_content_box_manager",
 *   "associated_table" = "base_box_managers",
 *   "association" = "above_content_box_managers.id = base_box_managers.id"
 * })
 */
class AboveContentBoxManager extends BaseBoxManager
{

}
