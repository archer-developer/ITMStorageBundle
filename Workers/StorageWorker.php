<?php

namespace ITM\StorageBundle\Workers;

use Mmoreram\GearmanBundle\Driver\Gearman;
use Symfony\Component\Console\Output\NullOutput;
use Mmoreram\GearmanBundle\Command\Util\GearmanOutputAwareInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Gearman\Work(
 *     name="StorageWorker",
 *     defaultMethod = "doNormal"
 * )
 */
class StorageWorker implements GearmanOutputAwareInterface
{
    protected $output;

    public function __construct()
    {
        $this->output = new NullOutput();
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name="testA",
     *     iterations = 1,
     *     timeout = 3,
     *     description = "This is a description"
     * )
     */
    public function testA(\GearmanJob $job)
    {
//        echo 'Job testA done!' . PHP_EOL;

//         file_put_contents('/home/alexsholk/test.txt', 'Test');
        $this->output->writeln('Job testA done!');
        return true;
    }
}