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

namespace Phabriks\Phit\Project;

use Phabriks\Phit\JsonFile as BaseJsonFile;
use JsonSchema\Validator;
use Composer\Json\JsonValidationException;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

/**
 * Phit Project configuration file class management
 *
 * extends the Phabriks\Phit\JsonFile to manage specific file schema validation
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
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Validates the schema of the current json file according to composer-schema.json rules
     *
     * @param  int $schema a JsonFile::*_SCHEMA constant
     *
     * @return boolean                   true on success
     * @throws \UnexpectedValueException
     */
    public function validateSchema($schema = self::STRICT_SCHEMA)
    {
        $content = file_get_contents($this->path);
        $data = json_decode($content);

        if (null === $data && 'null' !== $content) {
            self::validateSyntax($content, $this->path);
        }

        $schemaFile = dirname(__DIR__) . '/Resources/res/phit-schema.json';
        $schemaData = json_decode(file_get_contents($schemaFile));

        // @todo : see if it use uselfull to use this
        if ($schema === self::LAX_SCHEMA) {
            // $schemaData->additionalProperties = true;
            // $schemaData->properties->name->required = false;
            // $schemaData->properties->description->required = false;
        }

        $validator = new Validator();
        $validator->check($data, $schemaData);

        if (!$validator->isValid()) {
            $errors = array();
            foreach ((array) $validator->getErrors() as $error) {
                $errors[] = ($error['property'] ? $error['property'].' : ' : '').$error['message'];
            }
            throw new JsonValidationException('"'.$this->path.'" does not match the expected JSON schema', $errors);
        }

        return true;
    }
}
