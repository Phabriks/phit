<?php
/**
 * Phit core classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.fr>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */
namespace Phit;

use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

class Phit
{
    /**
     * Project configuration filename
     * @var string
     */
    const PROJECT_CONF_FILENAME = "phit.json";

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

    public $tasks = array();

    public $models = array();

    /**
     * Constructor
     *
     * @api
     */
    private function __construct()
    {
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
     * @return Phit
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Phit();
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
        $confFilePath = getcwd() . '/' . self::PROJECT_CONF_FILENAME;

        if ($filesystem->exists(array($confFilePath))) {
            $projectConf        = json_decode(file_get_contents($confFilePath), TRUE);
            $projectModel       = $projectConf['model'];
            $projectModelClass  = $this->models[$projectModel];
            $this->projectModel = new $projectModelClass;
            $this->projectConf  = $projectConf;
        }
    }

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
                $class = $namespace . '\\Models\\'
                       . preg_replace($patterns, $replacements, $iterator->getSubPathName());
                $id    = $class::$modelId;
                $models[$id] = $class;
            }
            $iterator->next();
        }
    }
}