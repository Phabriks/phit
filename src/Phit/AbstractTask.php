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
namespace Phit;

use Phit\Phit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Abstract Task class
 *
 * @category  Phit
 * @package   Phit.Tasks
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
abstract class AbstractTask extends Command
{
    /**
     * Environment name
     * @var string $env
     */
    protected $env;

    /**
     * Output stream
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * Input stream
     * @var InputInterface $input
     */
    protected $input;

    /**
     * Phit instance
     * @var Phit\Core\Phit $phitInstance
     */
    protected $phitInstance;

    /**
     * Filesystem instance
     * @var Symfony\Component\Filesystem\Filesystem $fsInstance
     */
    protected $fsInstance;

    /**
     * Dialog stream
     * @var Helper\DialogHelper $dialog
     */
    protected $dialog;


    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->phitInstance = Phit::getInstance();
        $this->fsInstance   = new Filesystem();
        $this->input        = $input;
        $this->output       = $output;
        $this->dialog       = $this->getHelperSet()->get('dialog');

        $debugStyle = new OutputFormatterStyle('red', 'yellow', array('bold', 'blink'));
        $this->output->getFormatter()->setStyle('debug', $debugStyle);
        $successStyle = new OutputFormatterStyle('white', 'green');
        $this->output->getFormatter()->setStyle('success', $successStyle);

        $this->output->writeln('');

        $this->runTask();

        $this->output->writeln('');
    }

    /**
     * Retrieve the environment id to use for the executed task
     *
     * @return void
     */
    protected function getEnvOption()
    {
        $projectConf = $this->phitInstance->getProjectConf();
        $envs = array_keys($projectConf['profiles']);

        $envQuestion = "<question>For which environment do you want to build your project ?</question>\n";
        foreach ($projectConf['profiles'] as $envId => $envInfo) {
            $envQuestion .= "<info>- " . $envId
                          . " (name: " . $envInfo['name'] . ", suffix: __" . $envInfo['suffix'] . "__)</info>\n";
        }

        $env = $this->input->getOption('env');
        if ($env) {
            try {
                $env = self::validateEnv($env);
            } catch (\Exception $error) {
                $env = false;
                $this->output->writeln(
                    $this->getDialog()->getHelperSet()->get('formatter')->formatBlock(
                        $error->getMessage(),
                        'error',
                        true
                    )
                );
            }
        }

        if (!$env) {
            $this->output->writeln('');
            $env = $this->dialog->askAndValidate(
                $this->output,
                $envQuestion,
                'Phit\AbstractTask::validateEnv'
            );
        }

        $this->env = $env;
    }

    /**
     * Get the project environment id
     *
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Get the phit instance
     *
     * @return \Phit\Phit
     */
    public function getPhitInstance()
    {
        return $this->phitInstance;
    }

    /**
     * Get command output
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Get command input
     *
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Get command dialog helper
     *
     * @return DialogHelper
     */
    public function getDialog()
    {
        return $this->dialog;
    }

    /**
     * Validate the environemnt id value
     *
     * @param string $value environment id
     *
     * @throws \Exception
     * @return string
     */
    public static function validateEnv($value)
    {
        $projectConf = Phit::getInstance()->getProjectConf();
        $envs = array_keys($projectConf['profiles']);

        if (!in_array($value, $envs)) {
            throw new \Exception('Invalid environment value : ' . $value);
        } else {
            return $value;
        }
    }

    /**
     * Format the json data so that it will be more readable in a file
     *
     * @param string $json Json data
     *
     * @return void
     */
    protected function formatJson($json)
    {
        $newJson     = false;
        $tab         = "  ";
        $newJson     = "";
        $indentLevel = 0;
        $inString    = false;
        $jsonObj     = json_decode($json);

        if ($jsonObj === true) {
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
                        break;
                    default:
                        $newJson .= $char;
                        break;
                }
            }
        }

        return $newJson;
    }
}

