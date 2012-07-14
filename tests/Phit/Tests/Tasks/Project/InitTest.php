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
 * Project Init Task Test class
 *
 * @category  Phit
 * @package   Phit.Tests
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class InitTest extends \PHPUnit_Framework_TestCase
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
    public function executeDataProvider()
    {
        return array(
            array(
                array(
                    'noProject',
                    'TestProject',
                    'ezpublish-ce',
                    'y',
                    'svn',
                    'https://sources.phabriks.com/test-project',
                    'y',
                    'y',
                    'y',
                    'y',
                ),
                '/Project initialized/'
            ),
            array(
                array('noProject', 'TestProject', 'ezpublish-ce', 'y', 'svn', '', '', 'n', 'n', 'n', 'n'),
                '/Project initialized/'
            ),
            array(
                array('fakeProject'),
                "/Invalid installation path '(.*)' directory doesn't exist/"
            ),
            array(
                array('noProject', 'TestProject', 'fakemodel'),
                "/Invalid project model/"
            ),
            array(
                array('noProject', 'TestProject', 'ezpublish-ce', 'y', 'cvs'),
                "/Invalid VCS type/"
            )
        );
    }

    /**
     * Test Project Initialisation task Execute method
     *
     * @param array  $inputs          inputs for each initialization questions
     * @param string $expectedTextMsg part of the message that should be displayed
     *                                while testing project conf file validity
     *
     * @return void
     * @dataProvider executeDataProvider
     */
    public function testExecute($inputs, $expectedTextMsg)
    {
        $inputsString = self::$testDataDir . implode("\n", $inputs) ."\n\n";

        $phit = Phit::getInstance();
        $task = $phit->getApplication()->get('project:init');
        $task->getHelperSet()->get('dialog')->setInputStream($this->getInputStream($inputsString));

        $phitTester = new PhitTester($phit);
        $phitTester->setProjectRootDir(self::$testDataDir . 'noProject');

        $taskTester = new TaskTester($task);
        try {
            $taskTester->execute(
                array_merge(array('command' => $task->getName())), array('interactive'=> true)
            );
            $phitConfFile = self::$testDataDir . array_shift($inputs) . '/' . \Phit\Phit::PROJECT_CONF_FILENAME;
            unlink($phitConfFile);
        } catch (\RuntimeException $e) {
            //Catch is empty as assertion code is the same with or without exceptions
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
}

