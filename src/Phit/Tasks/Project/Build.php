<?php
/**
 * Phit Tasks classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Tasks
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   SVN: $Id:$
 * @link      http://phit.phabriks.fr
 */
namespace Phit\Tasks\Project;

use Phit\Phit;
use Phit\AbstractTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Build Project Task class
 *
 * @category  Phit
 * @package   Phit.Tasks
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2012 Phabriks
 */
class Build extends AbstractTask
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
        ->setDescription('Build project')
        ->addOption('env', 'e', InputOption::VALUE_OPTIONAL, 'For which environment do you want to build your project')
        ;
    }

    /**
     * Run the current command
     *
     * @return void
     */
    protected function runTask()
    {
        $this->output->writeln('<info>Start building project</info>');
        $projectConf = $this->phitInstance->projectConf;

        if ($projectConf !== false) {
            // retrieve the environment option
            $this->getEnvOption();

            foreach ($projectConf['build']['steps'] as $step) {
                $stepMethod = 'launch' . ucfirst($step);
                $this->phitInstance->projectModel->$stepMethod($this, $this->getEnv());
            }

        } else {
            $this->output->writeln(
                $this->getDialog()->getHelperSet()->get('formatter')->formatBlock(
                    'No Phit project conf file available',
                    'error',
                    true
                )
            );
        }
    }
}

