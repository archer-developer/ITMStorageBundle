<?php

namespace ITM\StorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserRemoveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('itm:storage:user-remove')
            ->setDescription('Remove user')
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'User token'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getArgument('token');
        $repository = $this->getContainer()->get('doctrine')->getRepository('StorageBundle:User');

        if ($user = $repository->findOneByToken($token)) {
            if ($user->getDeletedAt()) {
                $output->writeln("User already deleted");
            } else {
                $user->setDeletedAt(new \DateTime());

                $em = $this->getContainer()->get('doctrine')->getManager();
                $em->persist($user);
                $em->flush();

                $output->writeln("User deleted");
            }
        } else {
            $output->writeln("User not exists");
        }
    }
}