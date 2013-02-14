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

namespace Phabriks\Phit\Command;

use Phabriks\Phit\Phit;
use Phabriks\Phit\Console\Application;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Base class for Composer commands
*
* @category  Phit
* @package   Phit.Core
* @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
* @copyright 2013 Phabriks
*/
abstract class Command extends BaseCommand
{
    /**
     * @var Phit
     */
    private $_phit;

    /**
     * @var Project
     */
    private $_project;

    /**
     * @var ContainerBuilder
     */
    private $_container;

    /**
     * @var IOInterface
     */
    private $_io;

    /**
     * @var OutputFormatter
     */
    private $_formatter;

    /**
     * Retrieve Phit instance
     *
     * @return Phit
     */
    public function getPhit()
    {
        if (null === $this->_phit) {
            $application = $this->getApplication();
            if ($application instanceof Application) {
                /* @var $application Application */
                $this->_phit = $application->getPhit();
            } else {
                throw new \RuntimeException(
                    'Could not create a Phabriks\Phit\Phit instance, you must inject '.
                    'one if this command is not used with a Phabriks\Phit\Console\Application instance'
                );
            }
        }

        return $this->_phit;
    }

    /**
     * Set the Phit instance to use with the command
     *
     * @param Phit $phit the phit instance
     *
     * @return void
     */
    public function setPhit(Phit $phit)
    {
        $this->_phit = $phit;
    }

    /**
     * Retrieve Phit instance
     *
     * @return Phit
     */
    public function getContainer()
    {
        if (null === $this->_container) {
            $application = $this->getApplication();
            if ($application instanceof Application) {
                /* @var $application Application */
                $this->_container = $application->getContainer();
            } else {
                throw new \RuntimeException(
                    'Could not create a Symfony\Component\DependencyInjection\ContainerBuilder instance'
                );
            }
        }

        return $this->_container;
    }

    /**
     * Retrieve the Project instance
     *
     * @return Project
     */
    public function getProject()
    {
        if (null === $this->_project) {
            $this->_project = $this->getContainer()->get('phit.project');
        }

        return $this->_project;
    }

    /**
     * Retrive the IOInterface
     *
     * @return IOInterface
     */
    public function getIO()
    {
        if (null === $this->_io) {
            $application = $this->getApplication();
            if ($application instanceof Application) {
                /* @var $application Application */
                $this->setIO($application->getIO());
            } else {
                $this->setIO(new NullIO());
            }
        }

        return $this->_io;
    }

    /**
     * Set the IO Interface to use for command input and output
     *
     * @param IOInterface $io Input Output interface
     *
     * @return void
     */
    public function setIO(IOInterface $io)
    {
        $this->_io = $io;
    }

    /**
     * Retrive the command OutputFormatter
     *
     * @return OutputFormatter
     */
    public function getFormatter()
    {
        if (null === $this->_formatter) {
            $this->setFormatter($this->getHelperSet()->get('formatter'));
        }

        return $this->_formatter;
    }

    /**
     * Set the formatter to use for command output
     *
     * @param FormatterHelper $formatter command output formatter
     *
     * @return void
     */
    public function setFormatter(FormatterHelper $formatter)
    {
        $this->_formatter = $formatter;
    }
}
