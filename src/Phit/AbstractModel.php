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

namespace Phit;

abstract class AbstractModel
{
    /**
     * Project model identifier
     * @var string $modelId
     */
    public static $modelId = '';

    /**
     * Checkstyle standard
     * @var string $csStandard
     */
    protected $csStandard = 'PEAR';

    /**
     * List of directories to check
     * @var array $checkDirs
     */
    protected $checkDirs = array(
        'src'
    );

    /**
     * List of file path patterns to be ignored from results
     * @var array $ignorePatterns
     */
    protected $ignorePatterns = array();

    /**
     *
     */
    public function launchPhpunit($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching phpunit build step');
    }

    public function launchCheckstyle($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching checkstyle build step');
    }

    public function launchMessdetection($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching mess detection build step');
    }

    public function launchCopypastedetection($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching copy paste detection build step');
    }

    public function reconfigure()
    {

    }
}