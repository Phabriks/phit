<?php

namespace Phit\Tests\Tasks;

use Phit\Phit;
use Phit\Tester\TaskTester;

class ValidateTest extends \PHPUnit_Framework_TestCase
{
    public function executeDataProvider()
    {
        return array(
            array(__DIR__ . '/../../../data/goodProject', '/The supplied JSON validates against the schema./'),
            array(__DIR__ . '/../../../data/badProject', '/JSON does not validate. Violations:/'),
        );
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute($path, $expectedTextMsg)
    {
        $phit = Phit::getInstance($path);
        $task = $phit->getApplication()->get('validate');

        $taskTester = new TaskTester($task);
        $taskTester->execute(array('command' => $task->getName()));
        $this->assertRegExp(
            $expectedTextMsg,
            $taskTester->getDisplay(),
            '->execute() validate the Phit project configuration file'
        );

        $taskTester->execute(array('command' => $task->getName()));
        $this->assertRegExp(
            '/Start validating phit conf file/',
            $taskTester->getDisplay(),
            '->execute() validate the Phit project configuration file'
        );
        unset($phit);
    }
}
