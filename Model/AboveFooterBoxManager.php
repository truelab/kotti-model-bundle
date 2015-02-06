<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "above_footer_box_managers",
 *   "type"  = "above_footer_box_manager",
 *   "associated_table" = "base_box_managers",
 *   "association" = "above_footer_box_managers.id = base_box_managers.id"
 * })
 */
class AboveFooterBoxManager extends BaseBoxManager
{

}
