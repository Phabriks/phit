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

/**
 * Phit eZ publish Enterprise Project Model class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
class Enterprise extends EZPublish
{
    /**
     * Model requirements to be set in composer.json file
     * @var array
     */
    protected $composerRequirements = array(
        'ezsystems/ezpublish5-standard' => 'dev-master'
    );
}
