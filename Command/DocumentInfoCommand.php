<?php

namespace ITM\StorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;

class DocumentInfoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('itm:storage:document-info')
            ->setDescription('Get document info by id')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Document id'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $storage = $this->getContainer()->get('itm.storage');
            $document = $storage->get($input->getArgument('id'));
            if (!$document) throw new \Exception('Document not found');

            $table = new Table($output);
            $table->setHeaders(['ID', 'Name', 'Internal Path', 'Attributes', 'Created at', 'Updated At']);
            $table->addRow([
                $document->getId(),
                $document->getName(),
                $document->getPath(),
                $document->getAttributes(),
                $document->getCreatedAt()->format('H:i d.m.Y'),
                $document->getUpdatedAt()->format('H:i d.m.Y')
            ]);
            $table->render();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}