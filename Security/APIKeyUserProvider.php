<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 26.8.15
 * Time: 19.54
 */

namespace ITM\StorageBundle\Security;


use Doctrine\Bundle\DoctrineBundle\Registry;
use ITM\StorageBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class APIKeyUserProvider implements UserProviderInterface
{
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function loadUserByUsername($apiKey)
    {
        $user = $this
            ->doctrine
            ->getRepository('StorageBundle:User')
            ->findOneBy(['token' => $apiKey]);

        if(!$user){
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'ITM\StorageBundle\Entity\User' === $class;
    }
}