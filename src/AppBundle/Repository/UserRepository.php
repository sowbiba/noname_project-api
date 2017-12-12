<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param string $username
     *
     * @return null|User
     */
    public function findValidUserByUsername($username)
    {
        $query = $this->createQueryBuilder('user')
            ->addSelect('role')
            ->join('user.role', 'role')
            ->where('user.username = :username')
            //->andWhere('user.active = 1')
            ->setParameter('username', $username)
            ->getQuery()
        ;

        try {
            $user = $query->getSingleResult();
        } catch (\Exception $e) {
            $user = null;
        }

        return $user;
    }
    /**
     * @param string $token
     *
     * @return null|User
     */
    public function findValidUserByToken($token)
    {
        $query = $this->createQueryBuilder('user')
            ->addSelect('role')
            ->join('user.role', 'role')
            ->where('user.token = :token')
            //->andWhere('user.active = 1')
            ->setParameter('token', $token)
            ->getQuery()
        ;

        try {
            $user = $query->getSingleResult();
        } catch (\Exception $e) {
            $user = null;
        }

        return $user;
    }
}
