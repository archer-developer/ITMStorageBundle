<?php

namespace ITM\StorageBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * Получение списка пользователей
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listUsers($limit = 100, $offset = 0)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from('StorageBundle:User', 'u')
            ->addOrderBy('u.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}