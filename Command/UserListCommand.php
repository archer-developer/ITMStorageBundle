<?php

namespace ITM\StorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
        $users = $em->createQuery(
            'SELECT u
            FROM StorageBundle:User u
            WHERE u.deleted_at IS NULL
            ORDER BY u.id ASC'
        )->getResult();

        $output->writeln("Id \t Created at \t\t Token");
        foreach ($users as $user) {
            $createdAt = $user->getCreatedAt()->format('H:i d.m.Y');
            $output->writeln("{$user->getId()} \t $createdAt \t {$user->getToken()}");
        }
    }
}