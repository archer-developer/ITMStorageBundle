<?php

namespace ITM\StorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class DocumentStoreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('itm:storage:document-store')
            ->setDescription('Store file with attributes in the storage')
            ->addArgument(
                'filepath',
                InputArgument::REQUIRED,
                'Path to file'
            )
            ->addArgument(
                'attributes',
                InputArgument::OPTIONAL,
                'JSON string'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $storage = $this->getContainer()->get('itm.storage');
            $document = $storage->store($input->getArgument('filepath'), $input->getArgument('attributes'));
            $output->writeln('Document with id ' . $document->getId() . ' created');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}