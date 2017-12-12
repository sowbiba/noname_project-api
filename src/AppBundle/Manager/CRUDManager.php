<?php

namespace AppBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use AppBundle\Exception\NotValidFormException;

abstract class CRUDManager extends EntityManager
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @param ContainerInterface $container
     * @param string             $entityClass
     * @param Form               $form
     */
    public function __construct(ContainerInterface $container, $entityClass = null, Form $form = null)
    {
        $this->form = $form;

        parent::__construct($container, $entityClass);
    }

    /**
     * @return object
     */
    public function createEmpty()
    {
        return new $this->entityClass();
    }

    /**
     * @param array $data
     *
     * @return object
     *
     * @throws NotValidFormException
     */
    public function create(array $data)
    {
        $entity = $this->createEmpty();

        $this->form->setData($entity);
        $this->form->submit($data);

        if (!$this->form->isValid()) {
            throw new NotValidFormException($this->form);
        }

        return $entity;
    }

    /**
     * @param array $data
     *
     * @return object
     *
     * @throws NotValidFormException
     */
    public function createAndSave(array $data)
    {
        $entity = $this->create($data);
        $this->save($entity);

        return $entity;
    }

    /**
     * @param object $entity
     * @param array  $data
     *
     * @return object
     *
     * @throws NotValidFormException
     */
    public function update($entity, array $data)
    {
        if ($this->entityClass !== get_class($entity)) {
            throw new \BadMethodCallException(
                sprintf(
                    'The object must be an instance of %s, instance of %s given.',
                    $this->entityClass,
                    get_class($entity)
                )
            );
        }

        $this->form->setData($entity);
        $this->form->submit($data, false);

        if (!$this->form->isValid()) {
            throw new NotValidFormException($this->form);
        }

        return $entity;
    }

    /**
     * @param object $entity
     * @param array  $data
     *
     * @return object
     *
     * @throws NotValidFormException
     */
    public function updateAndSave($entity, array $data)
    {
        $entity = $this->update($entity, $data);
        $this->save($entity);

        return $entity;
    }

    /**
     * @param object|array $entity
     */
    public function save($entity)
    {
        $entityManager = $this->getEntityManager();

        if (is_array($entity)) {
            foreach ($entity as $e) {
                if ($this->entityClass !== get_class($e)) {
                    throw new \BadMethodCallException(
                        sprintf(
                            'The object must be an instance of %s, instance of %s given.',
                            $this->entityClass,
                            get_class($e)
                        )
                    );
                }

                if (!$entityManager->contains($e)) {
                    $entityManager->persist($e);
                }
            }
        } else {
            if ($this->entityClass !== get_class($entity)) {
                throw new \BadMethodCallException(
                    sprintf(
                        'The object must be an instance of %s, instance of %s given.',
                        $this->entityClass,
                        get_class($entity)
                    )
                );
            }

            if (!$entityManager->contains($entity)) {
                $entityManager->persist($entity);
            }
        }

        $entityManager->flush();
    }

    /**
     * @param object|array $entity
     */
    public function delete($entity)
    {
        $entityManager = $this->getEntityManager();

        if (is_array($entity)) {
            foreach ($entity as $e) {
                if ($this->entityClass !== get_class($e)) {
                    throw new \BadMethodCallException(
                        sprintf(
                            'The object must be an instance of %s, instance of %s given.',
                            $this->entityClass,
                            get_class($e)
                        )
                    );
                }

                $entityManager->remove($e);
            }
        } else {
            if ($this->entityClass !== get_class($entity)) {
                throw new \BadMethodCallException(
                    sprintf(
                        'The object must be an instance of %s, instance of %s given.',
                        $this->entityClass,
                        get_class($entity)
                    )
                );
            }

            $entityManager->remove($entity);
        }

        $entityManager->flush();
    }
}
