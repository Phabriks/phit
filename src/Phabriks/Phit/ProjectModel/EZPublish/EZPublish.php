<?php
/**
 * Phit Core files
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */

namespace Phabriks\Phit\ProjectModel\EZPublish;

use Phabriks\Phit\ProjectModel\BaseModel;

/**
 * Phit eZ publish Project Model class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
class EZPublish extends BaseModel
{
    /**
     * Repository for Project model sources
     * @var string
     */
    protected $modelRepo = "https://subversion.assembla.com/svn/phabriks/tools/PhitProjectModels/eZpublish";
}
