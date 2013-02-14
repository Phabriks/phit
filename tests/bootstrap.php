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

error_reporting(E_ALL);

$loader = require __DIR__.'/../src/bootstrap.php';
$loader->add('Phabriks\Phit\Test', __DIR__);
