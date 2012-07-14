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

namespace Phit\Tests\Tasks;

use Phit\Phit;
use Phit\Tester\PhitTester;
use Phit\Tester\TaskTester;

/**
 * Validate Task Test class
 *
 * @category  Phit
 * @package   Phit.Tests
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class ValidateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Execute test method data provider
     *
     * @return array
     */
    public function executeDataProvider()
    {
        $testDataDirPath = __DIR__ . "/../../../data/";
        return array(
            array(
                $testDataDirPath . "goodProject",
                array(),
                "/The supplied JSON validates against the schema./"
            ),
            array(
                $testDataDirPath . "badProject",
                array('--file' => $testDataDirPath . 'goodProject/phit.json'),
                "/The supplied JSON validates against the schema./"
            ),
            array(
                $testDataDirPath . "badProject",
                array(),
                "/Json file '(.*)' does not validate. Violations:/"
            ),
            array(
                $testDataDirPath . "goodProject",
                array('--file' => $testDataDirPath . 'badProject/phit.json'),
                "/Json file '(.*)' does not validate. Violations:/"
            ),
            array(
                $testDataDirPath . "noProject",
                array(),
                "/Json file '(.*)' does not exist/"
            ),
            array(
                $testDataDirPath . "goodProject",
                array('--file' => $testDataDirPath . 'noProject/phit.json'),
                "/Json file '(.*)' does not exist/"
            ),
        );
    }

    /**
     * Test Validate task Execute method
     *
     * @param mixed  $path            the path the the project root directory
     * @param array  $taskParams      the options to pass to the task
     * @param string $expectedTextMsg part of the message that should be displayed
     *                                while testing project conf file validity
     *
     * @return void
     * @dataProvider executeDataProvider
     */
    public function testExecute($path, $taskParams, $expectedTextMsg)
    {
        try {
            $phit = Phit::getInstance();
            $task = $phit->getApplication()->get('validate');
            $phitTester = new PhitTester($phit);
            $phitTester->setProjectRootDir($path);

            $taskTester = new TaskTester($task);
            $taskTester->execute(array_merge(array('command' => $task->getName()), $taskParams));
        } catch (\Phit\Exceptions\HelperException $e) {

        }
        $this->assertRegExp(
            $expectedTextMsg,
            $taskTester->getDisplay(),
            '->execute() validate the Phit project configuration file'
        );
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

