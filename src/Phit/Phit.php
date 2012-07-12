<?php
/**
 * Phit Core classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */
namespace Phit;

use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Phit Core class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class Phit
{
    /**
     * Project configuration filename
     * @var string
     */
    const PROJECT_CONF_FILENAME = "phit.json";

    /**
     * Phit root directory
     * @var string $phitRootDir
     */
    protected $phitRootDir = false;

    /**
     * Project configuration
     * @var array $projectConf
     */
    public $projectConf = false;

    /**
     * Project model
     * @var \Phit\AbstractModel $projectModel
     */
    public $projectModel = false;

    /**
     * Phit instance singleton
     * @var Phit $_instance
     */
    private static $instance;

    /**
     * Console Application
     * @var mixed $application
     */
    private $application = false;

    /**
     * List of available tasks
     * @var array $tasks
     */
    public $tasks = array();

    /**
     * List of available models
     * @var array $models
     */
    public $models = array();

    /**
     * Constructor
     *
     * @param mixed $projectRootDir project root director, by default the current
     *                              directory where the phit command is launched
     *
     * @api
     */
    private function __construct($projectRootDir = false)
    {
        if ($projectRootDir === false) {
            $projectRootDir = getcwd();
        }
        $this->projectRootDir = $projectRootDir;
        $this->phitRootDir = dirname(dirname(__DIR__));
        $this->getAvailableTools();
        $this->loadProjectConf();
        $this->application = new Application('Phit', '1.0.0');

        foreach ($this->tasks as $taskName) {
            $task = new $taskName;
            $this->application->add($task);
        }
    }

    /**
     * Run application
     *
     * @return void
     */
    public function run()
    {
        return $this->application->run();
    }

    /**
     * Create new instance of class
     *
     * @param mixed $projectRootDir project root director, by default the current
     *                              directory where the phit command is launched
     *
     * @return Phit
     */
    public static function getInstance($projectRootDir = false)
    {
        if (is_null(self::$instance)) {
            self::$instance = new Phit($projectRootDir);
        }

        return self::$instance;
    }

    /**
     * Gets the projet config
     *
     * @return array
     */
    public function getProjectConf()
    {
        return $this->projectConf;
    }

    /**
     * Load Project conf file
     *
     * @return void
     */
    private function loadProjectConf()
    {
        $filesystem   = new Filesystem();
        echo $confFilePath = $this->projectRootDir . '/' . self::PROJECT_CONF_FILENAME;

        if ($filesystem->exists(array($confFilePath))) {
            $projectConf        = json_decode(file_get_contents($confFilePath), true);
            $projectModel       = $projectConf['model'];
            $projectModelClass  = $this->models[$projectModel];
            $this->projectModel = new $projectModelClass;
            $this->projectConf  = $projectConf;
        }
    }

    /**
     * Retrieve available tasks and models from the core and the bundles
     *
     * @return void
     */
    private function getAvailableTools()
    {
        $tasks        = array();
        $models       = array();

        $bundlesDir       = $this->phitRootDir . '/vendor/phitbundle';
        if (is_dir($bundlesDir)) {
            $bundles          = array();
            $bundleDirContent = scandir($bundlesDir);
            foreach ($bundleDirContent as $entry) {
                if (substr($entry, 0, 1) != '.' && is_dir($bundlesDir . '/' .$entry)) {
                    $bundles[] = $entry;
                }
            }

            if (count($bundles)) {
                foreach ($bundles as $bundle) {
                    $bundleDir       = $bundlesDir . '/' . $bundle . '/PhitBundle/'. ucfirst($bundle);
                    $bundleNamespace = '\\PhitBundle\\' . ucfirst($bundle);

                    if (is_dir($bundleDir .'/Tasks')) {
                        // Get Bundle tasks
                        $this->getAvailableTasks($bundleDir, $bundleNamespace, $tasks);
                    }

                    if (is_dir($bundleDir .'/Models')) {
                        // Get Bundle models
                        $this->getAvailableModels($bundleDir, $bundleNamespace, $models);
                    }
                }
            }
        }

        // Get Core tasks
        $this->getAvailableTasks(__DIR__, '\\Phit', $tasks);

        // Get Core models
        $this->getAvailableModels(__DIR__, '\\Phit', $models);

        $this->tasks  = $tasks;
        $this->models = $models;
    }

    /**
     * Retrieve available tasks
     *
     * @param string $dir       core / bundle classes directory
     * @param string $namespace core / bundle classes namespace
     * @param array  &$tasks    available tasks list
     *
     * @return void
     */
    protected function getAvailableTasks($dir, $namespace, &$tasks)
    {
        $patterns     = array('/\//', '/\.php/');
        $replacements = array('\\', '');
        $flags        = \FilesystemIterator::SKIP_DOTS;
        $iterator     = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir . '/Tasks', $flags)
        );
        while ($iterator->valid()) {
            if (!is_dir($iterator->key())) {
                $tasks[] = $namespace . "\\Tasks\\"
                         . preg_replace($patterns, $replacements, $iterator->getSubPathName());
            }
            $iterator->next();
        }
    }

    /**
     * Retrieve available Models
     *
     * @param string $dir       core / bundle classes directory
     * @param string $namespace core / bundle classes namespace
     * @param array  &$models   available models list
     *
     * @return void
     */
    protected function getAvailableModels($dir, $namespace, &$models)
    {
        $patterns     = array('/\//', '/\.php/');
        $replacements = array('\\', '');
        $flags        = \FilesystemIterator::SKIP_DOTS;
        $iterator     = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir . '/Models', $flags)
        );
        while ($iterator->valid()) {
            if (!is_dir($iterator->key())) {
                $class            = $namespace . '\\Models\\'
                                  . preg_replace($patterns, $replacements, $iterator->getSubPathName());
                $modelId          = $class::$modelId;
                $models[$modelId] = $class;
            }
            $iterator->next();
        }
    }

    /**
     * Get the application console
     *
     * @return \Symfony\Component\Console\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Get Phit available models
     *
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * Get Phit available models
     *
     * @return array
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Get Phit root directory path
     *
     * @return string
     */
    public function getPhitRootDir()
    {
        return $this->phitRootDir;
    }
}

