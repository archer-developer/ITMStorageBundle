<?php

namespace ITM\StorageBundle\Security;

use ITM\StorageBundle\Util\JsonAPITrait;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Created by PhpStorm.
 * User: archer
 * Date: 26.8.15
 * Time: 19.50
 */
class APIKeyAuthenticator implements SimplePreAuthenticatorInterface,
                                     AuthenticationFailureHandlerInterface
{
    use JsonAPITrait;

    public function createToken(Request $request, $providerKey)
    {
        $apiKey = $request->query->get('api_key');

        if (!$apiKey) {
            throw new BadCredentialsException('No API key found');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof APIKeyUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $apiKey = $token->getCredentials();

        $user = $userProvider->loadUserByUsername($apiKey);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->error('Authentication Failed', $exception->getMessage(), 403);
    }
}