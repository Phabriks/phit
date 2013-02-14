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

namespace Phabriks\Phit\Command\Project;

use Phabriks\Phit\Phit;
use Phabriks\Phit\Command\Command;
use Phabriks\Phit\Config\JsonFile;
use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Project initialization command class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
class Init extends Command
{
    /**
     * @var Config
     */
    protected $projectConf;

    /**
     * @var string
     */
    protected $projectPath;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('project:init')
            ->setDescription('Initialize new project')
            ->setHelp(
<<<EOT
The <info>project:init</info> command help will be completed as soon as possible.

<info>php phit.phar project:init</info>

EOT
            );
    }

    /**
     * Run the current command
     *
     * @param InputInterface  $input  command line input
     * @param OutputInterface $output command line output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io        = $this->getIO();
        $formatter = $this->getFormatter();

        $json         = JsonFile::formatJson(json_encode($this->projectConf));
        $phitConfFile = fopen($this->projectPath . '/' . Phit::PROJECT_CONF_FILENAME, 'w');
        fwrite($phitConfFile, $json);
        fclose($phitConfFile);

        $projectModel = $this->getProject()->getModel();

        $io->write('Export model sources from ' . $projectModel->getRepo());

        $io->write(
            $formatter->formatBlock('Project initialized', 'highlight', true)
        );
    }

    /**
     * Interact with the user to retrieve informations on the project to initialize
     *
     * @param InputInterface  $input  command line input
     * @param OutputInterface $output command line output
     *
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getIO();

        $io->write("<info>Start initializing project</info>", true);

        $this->projectPath = $io->askAndValidate(
            "<question>Where do you want to initialize your project (default: current location)?</question>\n",
            'Phabriks\Phit\Command\Project\Init::validateProjectInstallPath',
            false,
            getcwd()
        );

        $this->setCommonInfo($io);
        $this->setVcsInfo($io);
        $this->setBuildInfo($io);
        $this->setProfilesInfo($io);
    }

    /**
     * Define common informations for new project
     *
     * @param IOInterface $io Input Output interface
     *
     * @return void
     */
    protected function setCommonInfo(ConsoleIO $io)
    {
        $self = $this;
        // Ask common information
        $io->write("<info>[Common informations]</info>");
        $projectName = $io->ask(
            "<question>What will be your project name ? [default: SampleProject]</question>\n",
            'SampleProject'
        );
        $this->projectConf['name'] = $projectName;
        $this->projectConf['version'] = '0.0.1-snapshot';

        $projectModels = array_keys($this->getPhit()->getProjectModels());
        $projectModelQuestion = "<question>What kind of project do you want to initialize"
                              . " between the list :</question>\n";
        foreach ($projectModels as $model) {
            $projectModelQuestion .= "<info>- " . $model . "</info>\n";
        }
        $projectModel = $io->askAndValidate(
            $projectModelQuestion,
            function ($value) use ($self, $projectModels) {
                if (!in_array($value, $projectModels)) {
                    throw new \Exception('Invalid project model');
                } else {
                    return $value;
                }
            }
        );
        $this->projectConf['model'] = $projectModel;
    }

    /**
     * define vcs informations for new project
     *
     * @param IOInterface $io Input Output interface
     *
     * @return void
     */
    protected function setVcsInfo(ConsoleIO $io)
    {
        $io->write("\n<info>[VCS informations]</info>");
        $useVcs = $io->askConfirmation(
            "<question>Will you use a VCS with your project ? [yes,no]</question>\n"
        );
        if ($useVcs) {
            $this->projectConf['vcs'] = array();
            $vcsType = $io->askAndValidate(
                "<question>What kind of VCS will you use ?</question>\n",
                'Phabriks\Phit\Command\Project\Init::validateVcsType'
            );
            $this->projectConf['vcs']['type'] = $vcsType;

            $this->projectConf['vcs']['repositories'] = array();
            $vcsWriteUrl = $io->ask(
                "<question>What will be the write url of your project's repository ?</question>\n",
                ''
            );
            $this->projectConf['vcs']['repositories']['write'] = $vcsWriteUrl;

            $vcsReadUrl = $io->ask(
                "<question>What will be the read url of your project's repository ?</question>\n",
                $vcsWriteUrl
            );
            $this->projectConf['vcs']['repositories']['read'] = $vcsReadUrl;
        }
    }

    /**
     * Define the build information for the new project
     *
     * @param IOInterface $io Input Output interface
     *
     * @return void
     */
    protected function setBuildInfo(ConsoleIO $io)
    {
        $io->write("\n<info>[Build informations]</info>");
        $this->projectConf['build'] = array('steps' => array());
        $useCSBuildStep = $io->askConfirmation(
            "<question>Do you want to control the checkstyle of your code ? [yes,no]</question>\n"
        );
        if ($useCSBuildStep) {
            $this->projectConf['build']['steps'][] = 'checkstyle';
        }

        $useMDBuildStep = $io->askConfirmation(
            "<question>Do you want to use Mess Detection on your code ? [yes,no]</question>\n"
        );
        if ($useMDBuildStep) {
            $this->projectConf['build']['steps'][] = 'messdetection';
        }

        $useCPDBuildStep = $io->askConfirmation(
            "<question>Do you want to use Copy Paste Detection on your code ? [yes,no]</question>\n"
        );
        if ($useCPDBuildStep) {
            $this->projectConf['build']['steps'][] = 'copypastedetection';
        }

        $usePUnitBuildStep = $io->askConfirmation(
            "<question>Are you going to write PHP Unit tests for your code ? [yes,no]</question>\n"
        );
        if ($usePUnitBuildStep) {
            $this->projectConf['build']['steps'][] = 'phpunit';
        }
    }

    /**
     * Define the profiles information for the new project
     *
     * @param IOInterface $io Input Output interface
     *
     * @return void
     */
    protected function setProfilesInfo()
    {
        $this->projectConf['profiles'] = array(
            'dev' => array(
                    'name' => 'development',
                    'suffix' => 'DEV'
            ),
            'prod' => array(
                    'name' => 'production',
                    'suffix' => 'PROD',
                    'connection' => '',
                    'srcpath' => ''
            ),
            'ci' => array(
                    'name' => 'continuous integration',
                    'suffix' => 'CI'
            ),
            'remote-test' => array(
                    'name' => 'remote tests',
                    'suffix' => 'TESTS',
                    'connection' => '',
                    'srcpath' => ''
            )
        );
    }

    /**
     * Validate project installation path value
     *
     * @param string $value project installation path
     *
     * @return string
     * @throws \Exception
     */
    public static function validateProjectInstallPath($value)
    {
        if (!is_dir($value)) {
            throw new \Exception("Invalid installation path '$value' directory doesn't exist");
        } else {
            return $value;
        }
    }

    /**
     * Validate vcs type
     *
     * @param string $value vcs type
     *
     * @return string
     * @throws \Exception
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
     * @return string
     * @throws \Exception
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
     * @return string
     * @throws \Exception
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
     * @return string
     * @throws \Exception
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
