<?php

declare(strict_types=1);

namespace Application\Model\Hydrator;

use Application\Model\User;
use Zend\Hydrator\ObjectPropertyHydrator;

/**
 * @class UserHydrator
 * https://stackoverflow.com/questions/39145211/zend-framework-2-3-model-with-relations-to-itself-and-another-model
 */
class UserHydrator extends ObjectPropertyHydrator
{
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof User) {
            throw new \BadMethodCallException(sprintf(
                '%s expects the provided $object to be a User object)',
                __METHOD__
            ));
        }

        $user = new User();
        $user->exchangeArray($data);

        return $user;
    }
}
