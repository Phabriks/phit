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

namespace Phit\Tests\Tasks\Project;

use Phit\Phit;
use Phit\Tester\PhitTester;
use Phit\Tester\TaskTester;

/**
 * Project build Task Test class
 *
 * @category  Phit
 * @package   Phit.Tests
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class BuildTest extends \PHPUnit_Framework_TestCase
{
    protected static $testDataDir = false;

    /**
     * Setup data before tests
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$testDataDir = dirname(dirname(dirname(dirname(__DIR__)))) . '/data/';
    }

    /**
     * Execute test method data provider
     *
     * @return array
     */
    public function executeWithOptionsDataProvider()
    {
        return array(
            array('goodProject', array('--env' => 'dev'), '/launching (.*) build step/'),
            array('goodProject', array('-e' => 'dev'), '/launching (.*) build step/'),
            array('goodProject', array('--env' => 'foo'), '/Invalid environment value : foo/'),
            array('goodProject', array('-e' => 'foo'), '/Invalid environment value : foo/'),
            array('noProject', array('-e' => 'foo'), '/No Phit project conf file available/'),
        );
    }

    /**
     * Test Validate task Execute method with options
     *
     * @param mixed  $projectRootDir  the path the the project root directory
     * @param array  $taskParams      the options to pass to the task
     * @param string $expectedTextMsg part of the message that should be displayed
     *                                while testing project conf file validity
     *
     * @return void
     * @dataProvider executeWithOptionsDataProvider
     */
    public function testExecuteWithOptions($projectRootDir, $taskParams, $expectedTextMsg)
    {
        $phit = Phit::getInstance();
        $task = $phit->getApplication()->get('project:build');
        $task->getHelperSet()->get('dialog')->setInputStream($this->getInputStream(''));

        $phitTester = new PhitTester($phit);
        $phitTester->setProjectRootDir(self::$testDataDir . $projectRootDir);

        $taskTester = new TaskTester($task);
        try {
            $taskTester->execute(
                array_merge(array('command' => $task->getName()), $taskParams), array('interactive'=> true)
            );
        } catch (\RuntimeException $e) {

        }
        $this->assertRegExp(
            $expectedTextMsg,
            $taskTester->getDisplay(),
            '->execute() build the project for a given environment'
        );
    }

    /**
     * Execute test method data provider withtout options
     *
     * @return array
     */
    public function executeWithoutOptionsDataProvider()
    {
        return array(
            array('goodProject', 'dev', '/launching (.*) build step/'),
            array('goodProject', 'ci', '/launching (.*) build step/'),
            array('goodProject', 'foo', '/Invalid environment value : foo/'),
            array('noProject', '', '/No Phit project conf file available/'),
        );
    }

    /**
     * Test Validate task Execute method without options
     *
     * @param mixed  $projectRootDir  the path the the project root directory
     * @param string $envParam        the identifier of the environement
     * @param string $expectedTextMsg part of the message that should be displayed
     *                                while testing project conf file validity
     *
     * @return void
     * @dataProvider executeWithoutOptionsDataProvider
     */
    public function testExecuteWithoutOptions($projectRootDir, $envParam, $expectedTextMsg)
    {
        $phit = Phit::getInstance();
        $task = $phit->getApplication()->get('project:build');
        $task->getHelperSet()->get('dialog')->setInputStream($this->getInputStream($envParam));

        $phitTester = new PhitTester($phit);
        $phitTester->setProjectRootDir(self::$testDataDir . $projectRootDir);

        $taskTester = new TaskTester($task);
        try {
            $taskTester->execute(array('command' => $task->getName()), array('interactive'=> false));
        } catch (\RuntimeException $e) {
        }
        $this->assertRegExp(
            $expectedTextMsg,
            $taskTester->getDisplay(),
            '->execute() build the project for a given environment'
        );
    }

    /**
     * Get the input stream for a given input
     *
     * @param string $input input string
     *
     * @return stream
     */
    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }

    /**
     * Get the output stream
     *
     * @return StreamOutput
     */
    protected function getOutputStream()
    {
        return new StreamOutput(fopen('php://memory', 'r+', false));
    }
}

