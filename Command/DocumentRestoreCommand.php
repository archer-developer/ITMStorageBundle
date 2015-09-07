<?php

namespace ITM\StorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class DocumentRestoreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('itm:storage:document-restore')
            ->setDescription('Restore deleted document')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Document id'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $id = $input->getArgument('id');
            $storage = $this->getContainer()->get('itm.storage');
            $storage->restore($id);
            $output->writeln('Document #' . $id . ' restored');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}