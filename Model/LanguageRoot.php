<?php

namespace Truelab\KottiModelBundle\Model;

use Truelab\KottiModelBundle\TypeInfo\TypeInfo;

/**
 * @TypeInfo({
 *   "table" = "language_roots",
 *   "type"  = "language_root",
 *   "associated_table" = "documents",
 *   "association" = "language_roots.id = documents.id"
 * })
 */
class LanguageRoot extends Document implements LanguageRootInterface
{

}
