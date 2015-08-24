<?php

namespace ITM\StorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class DocumentGetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('itm:storage:document-get')
            ->setDescription('Copy file info specified path')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Document id'
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Target path'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $id = $input->getArgument('id');
            $path = realpath($input->getArgument('path'));
            if (!$path) throw new \Exception('Target directory not exists');
            if (!is_writable($path)) throw new \Exception('Target directory is not writable');

            $storage = $this->getContainer()->get('itm.storage');
            $document = $storage->get($id);
            $content = $storage->getContent($id);
            $path .= '/' . $document->getName();
            if (file_put_contents($path, $content) === false)
                throw new \Exception('Аn error occurred while writing the file');

            $output->writeln('File copied in ' . $input->getArgument('path'));
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}