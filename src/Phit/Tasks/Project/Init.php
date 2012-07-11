<?php
/**
 * Phit core classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.fr>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */
namespace Phit\Tasks\Project;

use Symfony\Component\Filesystem\Exception\ExceptionInterface;

use Phit\Phit;
use Phit\AbstractTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Init extends AbstractTask
{
    protected $projectConf;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('project:init')
        ->setDescription('Initialize project')
        ;
    }

    /**
     *
     *
     * @return void
     */
    protected function runTask()
    {
        $this->output->writeln("<info>Start initializing project</info>\n");
        $projectConf = array();

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

        $json = $this->json_format(json_encode($this->projectConf));
        $fp   = fopen($projectPath . '/' . Phit::PROJECT_CONF_FILENAME, 'w');
        fwrite($fp, $json);
        fclose($fp);

        $this->output->writeln("Project initialized");
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
        $projectModelQuestion = "<question>What kind of project do you want to initialize between the list :</question>\n";
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

//             $tagType = $this->dialog->askAndValidate(
//                 $this->output,
//                 "<question>What type of tag names do you want to use :</question>\n<info>- version\n- datetime</info>\n",
//                 'Phit\Tasks\Project\Init::validateTagType'
//             );
//             $this->projectConf['vcs']['tagType'] = $tagType;

//             $tagFormat = $this->dialog->askAndValidate(
//                 $this->output,
//                 "<question>What format do you want to use for your tag names: [release-##" . strtoupper($tagType) . "##]</question>\n",
//                 'Phit\Tasks\Project\Init::validate' . ucfirst($tagType) . 'TagFormat',
//                 false,
//                 'release-##' . strtoupper($tagType) . '##'
//             );
//             $this->projectConf['vcs']['tagFormat'] = $tagFormat;
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