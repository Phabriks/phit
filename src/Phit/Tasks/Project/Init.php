<?php
/**
 * Phit Tasks classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Tasks
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */
namespace Phit\Tasks\Project;

use Phit\AbstractTask;
use Symfony\Component\Filesystem\Exception\ExceptionInterface;

/**
 * Project Initialization Task class
 *
 * @category  Phit
 * @package   Phit.Tasks
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class Init extends AbstractTask
{
    protected $projectConf;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
        ->setName('project:init')
        ->setDescription('Initialize project')
        ;
    }

    /**
     * Run the current command
     *
     * @return void
     */
    protected function runTask()
    {
        $this->output->writeln("<info>Start initializing project</info>\n");

        $projectPath = $this->dialog->askAndValidate(
            $this->output,
            "<question>Where do you want to initialize your project (default: current location)?</question>\n",
            'Phit\Tasks\Project\Init::validateProjectInstallPath',
            false,
            getcwd()
        );

        $this->setCommonInfo();
        $this->setVcsInfo();
        $this->setBuildInfo();
        $this->setProfilesInfo();

        $json         = $this->formatJson(json_encode($this->projectConf));
        $phitConfFile = fopen($projectPath . '/' . Phit::PROJECT_CONF_FILENAME, 'w');
        fwrite($phitConfFile, $json);
        fclose($phitConfFile);

        $this->output->writeln("<success>\nProject initialized\n</success>");
    }

    /**
     * Define common informations for new project
     *
     * @return void
     */
    protected function setCommonInfo()
    {
        // Ask common information
        $this->output->writeln("<info>[Common informations]</info>");
        $projectName = $this->dialog->ask(
            $this->output,
            "<question>What will be your project name ? [default: SampleProject]</question>\n",
            'SampleProject'
        );
        $this->projectConf['name']    = $projectName;
        $this->projectConf['version'] = '0.0.1-snapshot';

        $projectModels = array_keys($this->phitInstance->models);
        $projectModelQuestion = "<question>What kind of project do you want to initialize"
                              . " between the list :</question>\n";
        foreach ($projectModels as $model) {
            $projectModelQuestion .= "<info>- " . $model . "</info>\n";
        }
        $projectModel = $this->dialog->askAndValidate(
            $this->output,
            $projectModelQuestion,
            'Phit\Tasks\Project\Init::validateProjectModel'
        );
        $this->projectConf['model'] = $projectModel;
    }

    /**
     * define vcs informations for new project
     *
     * @return void
     */
    protected function setVcsInfo()
    {
        $this->output->writeln("\n<info>[VCS informations]</info>");
        $useVcs = $this->dialog->askConfirmation(
            $this->output,
            "<question>Will you use a VCS with your project ? [yes,no]</question>\n"
        );
        if ($useVcs) {
            $this->projectConf['vcs'] = array();
            $vcsType = $this->dialog->askAndValidate(
                $this->output,
                "<question>What kind of VCS will you use ?</question>\n",
                'Phit\Tasks\Project\Init::validateVcsType'
            );
            $this->projectConf['vcs']['type'] = $vcsType;

            $this->projectConf['vcs']['repositories'] = array();
            $vcsWriteUrl = $this->dialog->ask(
                $this->output,
                "<question>What will be the write url of your project's repository ?</question>\n",
                ''
            );
            $this->projectConf['vcs']['repositories']['write'] = $vcsWriteUrl;

            $vcsReadUrl = $this->dialog->ask(
                $this->output,
                "<question>What will be the read url of your project\'s repository ?</question>\n",
                $vcsWriteUrl
            );
            $this->projectConf['vcs']['repositories']['read'] = $vcsReadUrl;
        }
    }

    /**
     * Define the build information for the new project
     *
     * @return void
     */
    protected function setBuildInfo()
    {
        $this->output->writeln("\n<info>[Build informations]</info>");
        $this->projectConf['build'] = array('steps' => array());
        $useCSBuildStep = $this->dialog->askConfirmation(
            $this->output,
            "<question>Do you want to control the checkstyle of your code ? [yes,no]</question>\n"
        );
        if ($useCSBuildStep) {
            $this->projectConf['build']['steps'][] = 'checkstyle';
        }

        $useMDBuildStep = $this->dialog->askConfirmation(
                $this->output,
                "<question>Do you want to use Mess Detection on your code ? [yes,no]</question>\n"
        );
        if ($useMDBuildStep) {
            $this->projectConf['build']['steps'][] = 'messdetection';
        }

        $useCPDBuildStep = $this->dialog->askConfirmation(
                $this->output,
                "<question>Do you want to use Copy Paste Detection on your code ? [yes,no]</question>\n"
        );
        if ($useCPDBuildStep) {
            $this->projectConf['build']['steps'][] = 'copypastedetection';
        }

        $usePUnitBuildStep = $this->dialog->askConfirmation(
                $this->output,
                "<question>Are you going to write PHP Unit tests for your code ? [yes,no]</question>\n"
        );
        if ($usePUnitBuildStep) {
            $this->projectConf['build']['steps'][] = 'phpunit';
        }
    }

    /**
     * Define the profiles information for the new project
     *
     * @return void
     */
    protected function setProfilesInfo()
    {
        $this->projectConf['profiles'] = array(
            'dev' => array(
                    'name'   => 'development',
                    'suffix' => 'DEV'
            ),
            'prod' => array(
                    'name'       => 'production',
                    'suffix'     => 'PROD',
                    'connection' => '',
                    'srcpath'    => ''
            ),
            'ci' => array(
                    'name'   => 'continuous integration',
                    'suffix' => 'CI'
            ),
            'remote-test' => array(
                    'name'       => 'remote tests',
                    'suffix'     => 'TESTS',
                    'connection' => '',
                    'srcpath'    => ''
            )
        );
    }

    /**
     * Validate project installation path value
     *
     * @param string $value project installation path
     *
     * @throws \Exception
     * @return string
     */
    public static function validateProjectInstallPath($value)
    {
        if (!is_dir($value)) {
            throw new \Exception("Invalid installation path: directory doesn't exist");
        } else {
            return $value;
        }
    }

    /**
     * Validate project model value
     *
     * @param string $value project model
     *
     * @throws \Exception
     * @return string
     */
    public static function validateProjectModel($value)
    {
        $projectModels = array_keys(Phit::getInstance()->models);

        if (!in_array($value, $projectModels)) {
            throw new \Exception('Invalid project model');
        } else {
            return $value;
        }
    }

    /**
     * Validate vcs type
     *
     * @param string $value vcs type
     *
     * @throws \Exception
     * @return string
     */
    public static function validateVcsType($value)
    {
        $vcsTypes = array(
            'svn'
        );
        if (!in_array($value, $vcsTypes)) {
            throw new \Exception('Invalid VCS type');
        } else {
            return $value;
        }
    }

    /**
     * Validate tag type
     *
     * @param string $value tag type
     *
     * @throws \Exception
     * @return string
     */
    public static function validateTagType($value)
    {
        $tagNameFormats = array(
            'version',
            'datetime'
        );
        if (!in_array($value, $tagNameFormats)) {
            throw new \Exception('Invalid tag name format');
        } else {
            return $value;
        }
    }

    /**
     * Validate version tag format
     *
     * @param string $value version tag format
     *
     * @throws \Exception
     * @return string
     */
    public static function validateVersionTagFormat($value)
    {
        if (!preg_match('/^([a-zA-Z0-9\-\.]*)##VERSION##([a-zA-Z0-9\-\.]*)$/', $value)) {
            throw new \Exception('Invalid version tag format');
        } else {
            return $value;
        }
    }

    /**
     * Validate the datetime tag format
     *
     * @param string $value datetime tag format
     *
     * @throws \Exception
     * @return string
     */
    public static function validateDatetimeTagFormat($value)
    {
        if (!preg_match('/^([a-zA-Z0-9\-\.]*)##DATETIME##([a-zA-Z0-9\-\.]*)$/', $value)) {
            throw new \Exception('Invalid datetime tag format');
        } else {
            return $value;
        }
    }
}
