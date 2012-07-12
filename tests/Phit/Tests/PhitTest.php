<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phit\Tests;

use Phit\Phit;

class PhitTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    public static function setUpBeforeClass()
    {
//         self::$fixturesPath = realpath(__DIR__.'/Fixtures/');
//         require_once self::$fixturesPath.'/FooCommand.php';
//         require_once self::$fixturesPath.'/Foo1Command.php';
//         require_once self::$fixturesPath.'/Foo2Command.php';
//         require_once self::$fixturesPath.'/Foo3Command.php';
    }

    protected function normalizeLineBreaks($text)
    {
        return str_replace(PHP_EOL, "\n", $text);
    }

    public function testConstructor()
    {
        $phit = Phit::getInstance();
        $this->assertEquals(
            array('help', 'list', 'project:build', 'project:init', 'validate'),
            array_keys($phit->getApplication()->all()),
            '__construct() registered the validate, project:build, project:init, help and list commands by default'
        );
    }

    public function testGetModels()
    {
        $phit = Phit::getInstance();
        $this->assertEquals(
            array('ezpublish-ce', 'ezpublish-ee'),
            array_keys($phit->getModels()),
            '->getModels() returns the registered models'
        );
    }

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
