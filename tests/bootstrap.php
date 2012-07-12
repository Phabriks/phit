<?php
/**
 * Phit Tests files
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

if (!is_readable(__DIR__.'/../vendor/autoload.php')) {
    echo <<<EOT
You must run `composer.phar install` to install the dependencies
before running the test suite.

EOT;
    exit(1);
}

$loader = include_once __DIR__.'/../vendor/autoload.php';
$loader->add('Phit\Tests', __DIR__);
$loader->register();

