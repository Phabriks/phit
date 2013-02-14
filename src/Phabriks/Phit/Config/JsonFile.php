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

namespace Phabriks\Phit\Config;

use Phabriks\Phit\JsonFile as BaseJsonFile;
use JsonSchema\Validator;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

/**
 * Phit configuration file class management
 *
 * extends the Phabriks\Phit\JsonFile to manage file path
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
class JsonFile extends BaseJsonFile
{
    /**
     * Initializes json file reader/parser.
     *
     * @param string $path path to a lockfile
     */
    public function __construct($path)
    {
        $this->path = dirname(__DIR__) . '/Resources/config/' . $path;
    }
}
