<?php

namespace ITM\StorageBundle\Workers;

use Mmoreram\GearmanBundle\Command\Util\GearmanOutputAwareInterface;
use Mmoreram\GearmanBundle\Driver\Gearman;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Воркер для выполнения удаленных вызовов событий
 *
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
        $params = json_decode($job->workload());
        $this->output->writeLn('Sending callback to ' . print_r($params, true));

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $params->URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, ['event' => json_encode($params->event)]);
        $out = curl_exec($curl);

        $this->output->writeLn('Response: ' . $out);

        curl_close($curl);

        return true;
    }


}