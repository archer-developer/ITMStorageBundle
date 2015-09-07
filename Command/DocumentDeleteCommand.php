<?php

namespace ITM\StorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class DocumentDeleteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('itm:storage:document-delete')
            ->setDescription('Delete document from the storage')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Document id'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Permanent delete'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $id = $input->getArgument('id');
            $force = $input->getOption('force');
            $storage = $this->getContainer()->get('itm.storage');
            $storage->delete($id, !$force);
            $output->writeln('Document #' . $id . ' deleted' . ($force ? ' permanently' : ''));
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}