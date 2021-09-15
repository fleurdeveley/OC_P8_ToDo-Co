<?php

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    protected function supports(string $attribute, $task): bool
    {
        return in_array($attribute, ['TASK_DELETE', 'TASK_EDIT', 'TASK_TOGGLE'])
            && $task instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $task, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'TASK_DELETE':
                return ($user === $task->getUsername()) || (in_array('ROLE_ADMIN', $user->getRoles())
                        && null === $task->getUsername());
            case 'TASK_EDIT':
                return true;
            case 'TASK_TOGGLE':
                return $user === $task->getUsername() || in_array('ROLE_ADMIN', $user->getRoles());
        }

        return false;
    }
}
