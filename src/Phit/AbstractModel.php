<?php
/**
 * Phit Models classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Models
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */

namespace Phit;

/**
 * Phit Abstract Model class
 *
 * @category  Phit
 * @package   Phit.Models
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
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
     * Launch phpunit tests
     *
     * @param \Phit\AbstractTask $task Phit task executed
     * @param string             $env  environment id for which the task is executed
     *
     * @return void
     */
    public function launchPhpunit($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching phpunit build step for : ' . $env);
    }

    /**
     * Launch checkstyle analysis
     *
     * @param \Phit\AbstractTask $task Phit task executed
     * @param string             $env  environment id for which the task is executed
     *
     * @return void
     */
    public function launchCheckstyle($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching checkstyle build step for : ' . $env);
    }

    /**
     * Launch mess detection analysis
     *
     * @param \Phit\AbstractTask $task Phit task executed
     * @param string             $env  environment id for which the task is executed
     *
     * @return void
     */
    public function launchMessdetection($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching mess detection build step for : ' . $env);
    }

    /**
     * Launch copy paste detection analysis
     *
     * @param \Phit\AbstractTask $task Phit task executed
     * @param string             $env  environment id for which the task is executed
     *
     * @return void
     */
    public function launchCopypastedetection($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching copy paste detection build step for : ' . $env);
    }


    /**
     * Launch sources reconfiguration for specified environment
     *
     * @param \Phit\AbstractTask $task Phit task executed
     * @param string             $env  environment id for which the task is executed
     *
     * @return void
     */
    public function launchReconfiguration($task, $env)
    {
        //@TODO : implement
        $task->getPhitInstance()->output->writeln('launching environment reconfiguration for : ' . $env);
    }
}

