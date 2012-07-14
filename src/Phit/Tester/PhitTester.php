<?php
/**
 * Phit Tester classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Tester
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */

namespace Phit\Tester;

use Phit\Phit;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Task Tester class
 *
 * Eases the testing of Phit applications.
 *
 * @category  Phit
 * @package   Phit.Tasks
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class PhitTester
{
    private $phitInstance;
    private $input;
    private $output;

    /**
     * Constructor.
     *
     * @param Application $application An Application instance to test.
     */
    public function __construct(Phit $phitInstance)
    {
        $this->phitInstance = $phitInstance;
        $this->phitInstance->setOutputStream(new StreamOutput(fopen('php://memory', 'w', false)));
    }

    /**
     * Executes the application.
     *
     * Available options:
     *
     *  * interactive: Sets the input interactive flag
     *  * decorated:   Sets the output decorated flag
     *  * verbosity:   Sets the output verbosity flag
     *
     * @param array $input   An array of arguments and options
     * @param array $options An array of options
     *
     * @return integer The command exit code
     */
    public function run(array $input, $options = array())
    {
        $this->input = new ArrayInput($input);
        if (isset($options['interactive'])) {
            $this->input->setInteractive($options['interactive']);
        }

        $this->output = new StreamOutput(fopen('php://memory', 'w', false));
        if (isset($options['decorated'])) {
            $this->output->setDecorated($options['decorated']);
        }
        if (isset($options['verbosity'])) {
            $this->output->setVerbosity($options['verbosity']);
        }

        return $this->phitInstance->run($this->input, $this->output);
    }

    /**
     * Set the project root directory path
     *
     * This is mainly useful for testing purpose.
     *
     * @param string $path project root directory path
     *
     * @return void
     */
    public function setProjectRootDir($path)
    {
        $this->phitInstance->setProjectRootDir($path);
    }

    /**
     * Gets the display returned by the last execution of the application.
     *
     * @return string The display
     */
    public function getDisplay()
    {
        rewind($this->output->getStream());

        return stream_get_contents($this->output->getStream());
    }

    /**
     * Gets the input instance used by the last execution of the application.
     *
     * @return InputInterface The current input instance
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Gets the output instance used by the last execution of the application.
     *
     * @return OutputInterface The current output instance
     */
    public function getOutput()
    {
        return $this->output;
    }
}

