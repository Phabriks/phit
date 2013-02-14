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

namespace Phabriks\Phit;

use JsonSchema\Validator;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

/**
 * Json file management class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
class JsonFile
{
    const LAX_SCHEMA = 1;
    const STRICT_SCHEMA = 2;

    const JSON_UNESCAPED_SLASHES = 64;
    const JSON_PRETTY_PRINT = 128;
    const JSON_UNESCAPED_UNICODE = 256;

    protected $path;

    /**
     * Initializes json file reader/parser.
     *
     * @param string $path path to a lockfile
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Checks whether json file exists.
     *
     * @return bool
     */
    public function exists()
    {
        return is_file($this->path);
    }

    /**
     * Reads json file.
     *
     * @return mixed
     */
    public function read()
    {
        try {
            $json = file_get_contents($this->path);
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not read '.$this->path."\n\n".$e->getMessage());
        }

        return static::parseJson($json, $this->path);
    }

    /**
     * Writes json file.
     *
     * @param array $hash    writes hash into json file
     * @param int   $options json_encode options
     *                       (defaults to JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
     *
     * @return void
     */
    public function write(array $hash, $options = 448)
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            if (file_exists($dir)) {
                throw new \UnexpectedValueException(
                    $dir.' exists and is not a directory.'
                );
            }
            if (!mkdir($dir, 0777, true)) {
                throw new \UnexpectedValueException(
                    $dir.' does not exist and could not be created.'
                );
            }
        }
        file_put_contents($this->path, static::encode($hash, $options). ($options & self::JSON_PRETTY_PRINT ? "\n" : ''));
    }

    /**
     * Parses json string and returns hash.
     *
     * @param string $json json string
     * @param string $file the json file
     *
     * @return mixed
     */
    public static function parseJson($json, $file = null)
    {
        $data = json_decode($json, true);
        if (null === $data && JSON_ERROR_NONE !== json_last_error()) {
            self::validateSyntax($json, $file);
        }

        return $data;
    }

    /**
     * Validates the syntax of a JSON string
     *
     * @param string $json json string
     * @param string $file the json file
     *
     * @return bool                      true on success
     * @throws \UnexpectedValueException
     * @throws JsonValidationException
     */
    protected static function validateSyntax($json, $file = null)
    {
        $parser = new JsonParser();
        $result = $parser->lint($json);
        if (null === $result) {
            if (defined('JSON_ERROR_UTF8') && JSON_ERROR_UTF8 === json_last_error()) {
                throw new \UnexpectedValueException('"'.$file.'" is not UTF-8, could not parse as JSON');
            }

            return true;
        }

        throw new ParsingException('"'.$file.'" does not contain valid JSON'."\n".$result->getMessage(), $result->getDetails());
    }

    /**
     * Format the json data so that it will be more readable in a file
     *
     * @param string $json Json data
     *
     * @return void
     */
    public static function formatJson($json)
    {
        $newJson     = false;
        $tab         = "  ";
        $newJson     = "";
        $indentLevel = 0;
        $inString    = false;
        $jsonObj     = json_decode($json);

        if ($jsonObj) {
            $json = json_encode($jsonObj);
            $len  = strlen($json);

            for ($c = 0; $c < $len; $c++) {
                $char = $json[$c];
                switch($char)
                {
                    case '{':
                    case '[':
                        if (!$inString) {
                            $newJson .= $char . "\n" . str_repeat($tab, $indentLevel+1);
                            $indentLevel++;
                        } else {
                            $newJson .= $char;
                        }
                        break;
                    case '}':
                    case ']':
                        if (!$inString) {
                            $indentLevel--;
                            $newJson .= "\n" . str_repeat($tab, $indentLevel) . $char;
                        } else {
                            $newJson .= $char;
                        }
                        break;
                    case ',':
                        if (!$inString) {
                            $newJson .= ",\n" . str_repeat($tab, $indentLevel);
                        } else {
                            $newJson .= $char;
                        }
                        break;
                    case ':':
                        if (!$inString) {
                            $newJson .= ": ";
                        } else {
                            $newJson .= $char;
                        }
                        break;
                    case '"':
                        if ($c > 0 && $json[$c-1] != '\\') {
                            $inString = !$inString;
                        }
                        // it should also use default treatment
                    default:
                        $newJson .= $char;
                        break;
                }
            }
        }

        return $newJson;
    }
}
