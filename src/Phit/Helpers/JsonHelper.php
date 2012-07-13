<?php
/**
 * Phit Helpers classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Helpers
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */
namespace Phit\Helpers;

use Phit\Phit;
use Phit\Exceptions\HelperException;
use JsonSchema\Validator;

/**
 * Json helper class
 *
 * @category  Phit
 * @package   Phit.Helpers
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class JsonHelper
{
    public static function validateFile($jsonFile, $jsonSchema)
    {
        if (!file_exists($jsonFile)) {
            throw new HelperException("Json file '" . $jsonFile . "' does not exist");
        }
        if (!file_exists($jsonSchema)) {
            throw new HelperException("Json schema '" . $jsonSchema . "' does not exist");
        }

        $validator = new Validator();
        $validator->check(
            json_decode(file_get_contents($jsonFile)),
            json_decode(file_get_contents($jsonSchema))
        );

        if ($validator->isValid()) {
            return true;
        } else {
            $errorMsg[] = "Json file '" . $jsonFile . "' does not validate. Violations:";
            foreach ($validator->getErrors() as $error) {
                $errorMsg[] = sprintf("[%s] %s", $error['property'], $error['message']);
            }
            throw new HelperException(implode("\n", $errorMsg));
        }
    }
}

