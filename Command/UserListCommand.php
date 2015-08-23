<?php

namespace ITM\StorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('itm:storage:user-list')
            ->setDescription('User list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $users = $em->getRepository('StorageBundle:User')->listUsers();

        $table = new Table($output);
        $table->setHeaders(['ID', 'Created at', 'Token']);
        foreach ($users as $user) {
            $table->addRow([
                $user->getId(),
                $user->getCreatedAt()->format('H:i d.m.Y'),
                $user->getToken(),
            ]);
        }
        $table->render();
    }
}