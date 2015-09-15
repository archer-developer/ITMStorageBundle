<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 15.9.15
 * Time: 21.30
 */

namespace ITM\StorageBundle\Command;

use ITM\StorageBundle\Entity\EventListener;
use ITM\StorageBundle\Util\JsonAPITrait;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientSubscribeCommand extends ContainerAwareCommand
{
    protected $client;

    protected function configure()
    {
        $this
            ->setName('itm:storage:client-subscribe')
            ->setDescription('Register remote listeners (the storage bundle is using as client)')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Connecting to remote storage server');

        $this->client = $this->getContainer()->get('itm.storage.remote_client');

        $table = new Table($output);
        $table->setHeaders(['Event', 'Status']);

        $step = 10;
        $bar = new ProgressBar($output, count(EventListener::getAvailableEvents()) * $step);
        $bar->start();

        foreach(EventListener::getAvailableEvents() as $event_code => $event_name){
            $table->addRow([
                EventListener::getAvailableEvents()[$event_code],
                $this->connect($event_code),
            ]);
            $bar->advance($step);
        }

        $bar->finish();
        $output->writeln('');

        $table->render();
    }

    protected function connect($event)
    {
        try{
            $response = $this->client->addEventListener($event);
        }
        catch(HttpException $e){
            return 'HTTP Code: ' . $e->getStatusCode() . ', ' . $e->getMessage();
        }

        if($response->status == JsonAPITrait::$STATUS_SUCCESS){
            return 'OK';
        }

        return 'ERROR: ' . $response->response->error_message;
    }
}