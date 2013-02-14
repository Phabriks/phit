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

use Phabriks\Phit\JsonFile;

/**
 * Phit Configuration class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
class Config
{
    private $_config;

    public function __construct(JsonFile $configFile)
    {
        if ($configFile->exists()) {
            $configData = $configFile->read();
        } else {
            throw new \RuntimeException('no configuration file ' . $configFile->getPath());
        }
        $this->_config = $configData;
    }

    /**
     * Merges new config values with the existing ones (overriding)
     *
     * @param array $config
     */
    public function merge(array $config)
    {
        // override defaults with given config
        if (!empty($config['config']) && is_array($config['config'])) {
            foreach ($config['config'] as $key => $val) {
                if (in_array($key, array('commands', 'projectModels')) && isset($this->_config[$key])) {
                    $this->_config[$key] = array_merge($this->_config[$key], $val);
                } else {
                    $this->_config[$key] = $val;
                }
            }
        }
    }

    /**
     * Returns a setting
     *
     * @param  string $key
     *
     * @return mixed
     * @throws RuntimeException if no configuration data found for provided key
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->_config)) {
            $return = $this->_config[$key];
        } else {
            throw new \RuntimeException('No configuration found for key ' . $key);
        }

        return $return;
    }

    public function all()
    {
        $all = array();
        foreach (array_keys($this->_config) as $key) {
            $all['config'][$key] = $this->get($key);
        }

        return $all;
    }

    /**
     * Checks whether a setting exists
     *
     * @param  string $key configuration key string
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->_config);
    }
}
