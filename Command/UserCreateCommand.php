<?php

namespace ITM\StorageBundle\Command;

use ITM\StorageBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('itm:storage:user-create')
            ->setDescription('Create user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $token = password_hash(microtime() . rand(0, 1000), PASSWORD_BCRYPT);
        $user->setToken($token);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $output->writeln('User token:');
        $output->writeln($token);
    }
}