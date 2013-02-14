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

use Phabriks\Phit\Config\JsonFile;
use Phabriks\Phit\ProjectModel\BaseModel;
use Phabriks\Phit\Config;

/**
 * Phit Core class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
class Phit
{
    const VERSION = '@package_version@';

    const PROJECT_CONF_FILENAME = 'phit.json';

    /**
     * ASCII Art logo to be displayed by console
     * @var string
     */
    private $_logo = <<<EOT
    ____  __    _ __
   / __ \/ /_  (_) /_
  / /_/ / __ \/ / __/
 / ____/ / / / / /_
/_/   /_/ /_/_/\__/

EOT
    ;

    /**
     * Phit configuration
     * @var Config
     */
    private $_config;

    // /**
    //  * Project instance
    //  * @var Project
    //  */
    // private $_project;

    /**
     * Phit project models
     * @var array of ProjectModels
     */
    private $_projectModels;

    /**
     * Class constructor
     *
     * @param Config $config Application configuration
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config);
    }

    /**
     * Retrieve logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->_logo;
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
     * Get commands infos
     *
     * @return array
     */
    public function getCommands()
    {
        $commands       = array();
        $commandsConfig = $this->getConfig()->get('commands');

        foreach ($commandsConfig as $commandName => $commandConfig) {
            if (array_key_exists('class', $commandConfig)) {
                $commands[$commandName] = $commandConfig['class'];
            }
            if (array_key_exists('subcommands', $commandConfig)) {
                foreach ($commandConfig['subcommands'] as $subcommandName => $subcommandConfig) {
                    if (array_key_exists('class', $subcommandConfig)) {
                        $commands[$commandName . ':' . $subcommandName] = $subcommandConfig['class'];
                    }
                }
            }
        }

        return $commands;
    }

    /**
     * Retrieve project models
     *
     * @return array
     */
    public function getProjectModels()
    {
        if (!$this->_projectModels) {
            $this->_projectModels = array();
            $projectModelsConfig = $this->getConfig()->get('models');

            foreach ($projectModelsConfig as $modelName => $modelConfig) {
                if (array_key_exists('class', $modelConfig)) {
                    $this->_projectModels[$modelName] = $modelConfig['class'];
                }
                if (array_key_exists('submodels', $modelConfig)) {
                    foreach ($modelConfig['submodels'] as $submodelName => $submodelConfig) {
                        if (array_key_exists('class', $submodelConfig)) {
                            $this->_projectModels[$modelName . '.' . $submodelName] = $submodelConfig['class'];
                        }
                    }
                }
            }
        }

        return $this->_projectModels;
    }

    /**
     * Retrieve Phit project configuration file
     *
     * @return string Phit project configuration filename
     */
    public function getPhitFile()
    {
        return getenv('PHIT') ?: self::PROJECT_CONF_FILENAME;
    }

    // /**
    //  * Store the project
    //  *
    //  * @param Project $project phit configuration
    //  *
    //  * @return void
    //  */
    // public function setProject(Project $project)
    // {
    //     $this->_project = $project;
    // }

    // /**
    //  * Retrieve project
    //  *
    //  * @return Project
    //  */
    // public function getProject()
    // {
    //     return $this->_project;
    // }

    // /**
    //  * Retrieve project configuration
    //  *
    //  * @return Config Phit Project Configuration object
    //  */
    // public function getProjectConfig()
    // {
    //     // load Phit project configuration
    //     if (!$this->_projectConfig) {
    //         $projectConfigFile = $this->getPhitFile();

    //         $file = new JsonFile($projectConfigFile, new RemoteFilesystem($this->getIO()));

    //         if (!$file->exists()) {
    //             if ($projectConfig === 'phit.json') {
    //                 $message = 'Phit could not find a phit.json file in '.getcwd();
    //             } else {
    //                 $message = 'Phit could not find the config file: ' . $projectConfigFile;
    //             }
    //             $instructions = 'To initialize a project, use the project initialization command or '
    //                           . 'create a phit.json file as described on http://phit-project.com/ ';
    //             throw new \InvalidArgumentException($message . PHP_EOL . $instructions);
    //         }

    //         $file->validateSchema(JsonFile::LAX_SCHEMA);
    //         $projectConfig = $file->read();
    //         $config = new Config();

    //         $this->_projectConfig = $config->merge($projectConfig);
    //     }

    //     return $this->_projectConfig;
    // }

    /**
     * Retrieve the Project Model instance corresponding to the provided key
     *
     * @param string $modelKey project model identification string
     *
     * @return BaseModel project model instance
     */
    public function getModel($modelKey)
    {
        $projectModels = $this->getProjectModels();
        if (array_key_exists($modelKey, $projectModels)) {
            $model = new $projectModels[$modelKey];
        } else {
            throw new \InvalidArgumentException('No project model available for key : ' . $modelKey);
        }

        return $model;
    }
}
