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
        return array(
            array(__DIR__ . '/../../../data/goodProject', '/The supplied JSON validates against the schema./'),
            array(__DIR__ . '/../../../data/badProject', '/JSON does not validate. Violations:/'),
        );
    }

    /**
     * Test Validate task Execute method
     *
     * @param mixed  $path            the path the the project root directory
     * @param string $expectedTextMsg part of the message that should be displayed
     *                                while testing project conf file validity
     *
     * @return void
     * @dataProvider executeDataProvider
     */
    public function testExecute($path, $expectedTextMsg)
    {
        $phit = Phit::getInstance();
        $phit->setProjectRootDir($path);
        $task = $phit->getApplication()->get('validate');

        $taskTester = new TaskTester($task);
        $taskTester->execute(array('command' => $task->getName()));
        $this->assertRegExp(
            $expectedTextMsg,
            $taskTester->getDisplay(),
            '->execute() validate the Phit project configuration file'
        );
    }
}

