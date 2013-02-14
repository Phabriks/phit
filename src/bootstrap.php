<?php
/**
* Phit Core files
*
* PHP VERSION 5
*
* @category  Phit
* @package   Phit.Core
* @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
* @copyright 2013 Phabriks
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @version   SVN: $Id:$
* @link      http://phit.phabriks.fr
*/

/**
 * Phit bootstrap file
*
* @category  Phit
* @package   Phit.Core
* @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
* @copyright 2013 Phabriks
 */
function includeIfExists($file)
{
    if (file_exists($file)) {
        return include $file;
    }
}

if ((!$loader = includeIfExists(__DIR__.'/../vendor/autoload.php'))
    && (!$loader = includeIfExists(__DIR__.'/../../../autoload.php'))) {
    echo 'You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL;
    exit(1);
}

return $loader;
