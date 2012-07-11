<?php
/**
 * Phit core classes
 *
 * PHP VERSION 5
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.fr>
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

class Build extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('project:build')
        ->setDescription('Build project')
        ->addOption('env', 'e', InputOption::VALUE_OPTIONAL, 'For which environment do you want to build your project')
        ;
    }

    protected function runTask()
    {
        $this->output->writeln('<info>Start building project</info>');
        $projectConf = $this->phitInstance->projectConf;

        // retrieve the environment option
        $this->getEnvOption();

        foreach ($projectConf['build']['steps'] as $step) {
            $stepMethod = 'launch' . ucfirst($step);
            $this->phitInstance->projectModel->$stepMethod($this, $this->getEnv());
        }
    }
}