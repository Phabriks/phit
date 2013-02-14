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

use Phabriks\Phit\Project\JsonFile as ProjectJsonFile;
use Phabriks\Phit\Config;
use Phabriks\Phit\Phit;

/**
 * Phit Project class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
class Project
{
    /**
     * @var Phit
     */
    private $_phit;

    /**
     * Project configuration
     * @var Config
     */
    private $_config;

    /**
     * Class constructor
     *
     * @param Config $config Application configuration
     */
    public function __construct(Phit $phit, Config $config)
    {
        $this->setPhit($phit);
        $this->setConfig($config);
    }

    /**
     * Retrieve Phit instance
     *
     * @return Phit
     */
    public function getPhit()
    {
        return $this->_phit;
    }

    /**
     * @param Phit $phit
     */
    public function setPhit(Phit $phit)
    {
        $this->_phit = $phit;
    }

    /**
     * Store the configuration
     *
     * @param Config $config phit configuration
     *
     * @return void
     */
    public function setConfig(Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Retrieve project configuration
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Retrieve the Project Model instance corresponding to the key defined in the project configuration
     *
     * @return string project model key
     */
    public function getConfigData($configKey)
    {
        $configData = $this->getConfig()->get($configKey);
        // if (!$configData) {
        //     throw new \InvalidArgumentException('No project model defined in project configuration file');
        // }

        return $configData;
    }

    /**
     * Retrieve the Project Model instance corresponding to the key defined in the project configuration
     *
     * @return BaseModel project model instance
     */
    public function getModel()
    {
        $projectModelKey = $this->getConfigData('model');
        if ($projectModelKey) {
            $model = $this->getPhit()->getModel($projectModelKey);
        } else {
            throw new \InvalidArgumentException('No project model defined in project configuration file');
        }

        return $model;
    }
}
