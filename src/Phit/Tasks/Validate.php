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
namespace Phit\Tasks;

use Phit\Phit;
use Phit\AbstractTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use JsonSchema\Validator;

class Validate extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('validate')
        ->setDescription('Validate phit.jon project configuration file')
        ;
    }

    protected function runTask()
    {
        $this->output->writeln("<info>Start validating phit conf file</info>\n");
        $projectConf = $this->phitInstance->projectConf;
        $validator = new Validator();
        $validator->check(
            json_decode(json_encode($projectConf)),
            json_decode(file_get_contents(dirname(__DIR__) . '/phit.json.schema'))
        );

        if ($validator->isValid()) {
            $this->output->writeln("<success>\nThe supplied JSON validates against the schema.\n</success>");
        } else {
            $errorMsg = "\nJSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                $errorMsg .= sprintf("[%s] %s",$error['property'], $error['message']) . "\n";
            }
            $this->output->writeln("<error>". $errorMsg ."</error>");
        }
    }
}