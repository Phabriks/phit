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

namespace Phabriks\Phit\Command\Project;

use Phabriks\Phit\Phit;
use Phabriks\Phit\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
* Project build command class
*
* @category  Phit
* @package   Phit.Core
* @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
* @copyright 2013 Phabriks
*/
class Build extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('project:build')
            ->setDescription('Launch static analysis and tests')
            ->setHelp(
<<<EOT
The <info>project:build</info> command help will be completed as soon as possible.

<info>php phit.phar project:build</info>

EOT
            );
    }

    /**
     * Run the current command
     *
     * @param InputInterface  $input  command line input
     * @param OutputInterface $output command line output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Not implemented yet.</info>");
    }
}
