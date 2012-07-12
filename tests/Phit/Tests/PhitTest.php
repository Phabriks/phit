<?php
/**
 * Phit Tests classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Tests
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */

namespace Phit\Tests;

use Phit\Phit;

/**
 * Phit Application Test class
 *
 * @category  Phit
 * @package   Phit.Tests
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class PhitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Phit getInstance() test method
     *
     * @return void
     */
    public function testGetInstance()
    {
        $phit = Phit::getInstance();
        $this->assertEquals(
            array('help', 'list', 'project:build', 'project:init', 'validate'),
            array_keys($phit->getApplication()->all()),
            '__construct() registered the validate, project:build, project:init, help and list commands by default'
        );
    }

    /**
     * Phit getModels() test method
     *
     * @return void
     */
    public function testGetModels()
    {
        $phit = Phit::getInstance();
        $this->assertEquals(
            array('ezpublish-ce', 'ezpublish-ee'),
            array_keys($phit->getModels()),
            '->getModels() returns the registered models'
        );
    }

    /**
     * Phit getTasks() test method
     *
     * @return void
     */
    public function testGetTasks()
    {
        $tasks = array(
            '\Phit\Tasks\Project\Build',
            '\Phit\Tasks\Project\Init',
            '\Phit\Tasks\Validate',
        );
        $phit = Phit::getInstance();
        $this->assertEquals(
            $tasks,
            $phit->getTasks(),
            '->getTasks() returns the registered tasks'
        );
    }
}
