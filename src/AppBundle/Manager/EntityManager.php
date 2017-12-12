<?php

namespace AppBundle\Manager;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * When extending this abstract class, you MUST define the $entityClass variable.
 */
abstract class EntityManager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var DoctrineEntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var DoctrineEntityManager
     */
    protected $repository;

    /**
     * @param ContainerInterface $container
     * @param string             $entityClass
     */
    public function __construct(ContainerInterface $container, $entityClass)
    {
        $this->container = $container;
        $this->entityClass = $entityClass;

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->repository = $this->entityManager->getRepository($this->entityClass);
    }

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed    $id          The identifier.
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @return DoctrineEntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return DoctrineEntityManager|\Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
