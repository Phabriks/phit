<?php
/**
 * Phit Models classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Models
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */
namespace Phit\Models;

use Phit\AbstractModel;

/**
 * Phit eZpublish Enterprise Edition Model class
 *
 * @category  Phit
 * @package   Phit.Models
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class EzpublishEE extends AbstractModel
{
    /**
     * Project model identifier
     * @var string $modelId
     */
    public static $modelId = 'ezpublish-ee';

    /**
     * Checkstyle standard
     * @var string $csStandard
     */
    protected $csStandard = 'ezpublish';

    /**
     * List of directories to check
     * @var array $checkDirs
     */
    protected $checkDirs = array(
        'src/extension'
    );
}

