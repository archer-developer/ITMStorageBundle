<?php

namespace ITM\StorageBundle\Workers;

use Mmoreram\GearmanBundle\Command\Util\GearmanOutputAwareInterface;
use Mmoreram\GearmanBundle\Driver\Gearman;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Gearman\Work(
 *     name = "EventWorker",
 *     description = "Send remote notifications",
 *     defaultMethod = "doBackground"
 * )
 */
class EventWorker implements GearmanOutputAwareInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->output = new NullOutput();
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "remoteCallback",
     *     description = "Call remote url for receive event"
     * )
     */
    public function remoteCallback(\GearmanJob $job)
    {
        $this->output->writeLn('Job testA done!');

        return true;
    }


}