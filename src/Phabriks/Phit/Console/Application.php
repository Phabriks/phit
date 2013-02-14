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

namespace Phabriks\Phit\Console;

use Phabriks\Phit\Phit;
use Phabriks\Phit\Command;
use Composer\IO\ConsoleIO;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
* The console application that handles the commands
*
* @category Phit
* @package Phit.Core
* @author Guillaume Maïssa <guillaume.maissa@phabriks.com>
* @copyright 2013 Phabriks
*/
class Application extends BaseApplication
{
    /**
     * @var Phit
     */
    protected $phit;

    /**
     * @var IOInterface
     */
    protected $io;

    protected $container;

    /**
     * Console application constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.yml');

        if (function_exists('ini_set')) {
            ini_set('xdebug.show_exception_trace', false);
            ini_set('xdebug.scream', false);

        }
        if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
            date_default_timezone_set(@date_default_timezone_get());
        }
        //ErrorHandler::register();
        parent::__construct('Phit', Phit::VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $styles = $this->createAdditionalStyles();
            $formatter = new OutputFormatter(null, $styles);
            $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
        }

        return parent::run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->io = new ConsoleIO($input, $output, $this->getHelperSet());

        $this->io->write($this->getPhit()->getLogo());

        if (version_compare(PHP_VERSION, '5.3.2', '<')) {
            $this->io->write(
                '<warning>Phit only officially supports PHP 5.3.2 and above, you will most likely encounter problems ' .
                'with your PHP '.PHP_VERSION.', upgrading is strongly recommended.</warning>'
            );
        }

        if ($input->hasParameterOption('--profile')) {
            $startTime = microtime(true);
        }

        $oldWorkingDir = getcwd();
        $this->switchWorkingDir($input);

        $result = parent::doRun($input, $output);

        chdir($oldWorkingDir);

        if (isset($startTime)) {
            $this->io->write(
                '<info>Memory usage: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB (peak: ' .
                round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB), time: ' .
                round(microtime(true) - $startTime, 2) . 's'
            );
        }

        return $result;
    }

    /**
     * Use a different working directory then the one where the command has been launched
     *
     * @param InputInterface $input
     *
     * @return void
     * @throws \RuntimeException
     */
    private function switchWorkingDir(InputInterface $input)
    {
        $workingDir = $input->getParameterOption(array('--working-dir', '-d'), getcwd());
        if (!is_dir($workingDir)) {
            throw new \RuntimeException('Invalid working directory specified.');
        }
        chdir($workingDir);
    }

    /**
     * Retrive Phit object instance
     *
     * @param bool $required
     *
     * @return Phit
     */
    public function getPhit($required = true)
    {
        if (null === $this->phit) {
            try {
                $this->phit = $this->getContainer()->get('phit');

            } catch (\InvalidArgumentException $e) {
                if ($required) {
                    $this->getIO()->write('<warning>' . $e->getMessage() . '</warning>');
                    exit(1);
                }
            }
        }

        return $this->phit;
    }

    /**
     * Get IO Interface
     *
     * @return IOInterface
     */
    public function getIO()
    {
        return $this->io;
    }

    /**
     * Get IO Interface
     *
     * @return IOInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Initializes all the composer commands
     *
     * @return array of Symfony\Component\Console\Command\Command
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $phitCommands = $this->getPhit()->getCommands();
        foreach ($phitCommands as $phitCommand) {
            $commands[] = new $phitCommand();
        }

        return $commands;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(
            new InputOption('--profile', null, InputOption::VALUE_NONE, 'Display timing and memory usage information')
        );
        $definition->addOption(
            new InputOption(
                '--working-dir',
                '-d',
                InputOption::VALUE_REQUIRED,
                'If specified, use the given directory as working directory.'
            )
        );
        $definition->addOption(
            new InputOption('--dev', null, InputOption::VALUE_NONE, 'For Phit development purpose only')
        );

        return $definition;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();

        //$helperSet->set(new DialogHelper());

        return $helperSet;
    }

    protected function createAdditionalStyles()
    {
        return array(
            'highlight' => new OutputFormatterStyle('green'),
            'warning'   => new OutputFormatterStyle('black', 'yellow'),
        );
    }
}
