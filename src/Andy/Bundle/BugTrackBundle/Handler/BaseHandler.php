<?php

namespace Andy\Bundle\BugTrackBundle\Handler;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BaseHandler
 */
abstract class BaseHandler
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Injects token storage
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get current user
     *
     * @return UserInterface|null
     */
    protected function getUser()
    {
        if ($this->tokenStorage->getToken()) {
            return $this->tokenStorage->getToken()->getUser();
        }

        return null;
    }
}
