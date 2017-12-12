<?php

namespace AppBundle\Event\Subscriber;

use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Subscriber of the User entity.
 */
class UserSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    private $inserts = [];

    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     *
     * @throws \Exception
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof User) {
                $entity->setPassword(
                    hash($entity->getAlgorithm(), $entity->getSalt().strtolower($entity->getPassword()))
                );

                $entity->setUpdatedAt(new \DateTime('now'));

                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata('AppBundle:User'), $entity);

                $this->inserts[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof User) {
                $changes = $uow->getEntityChangeSet($entity);

                if (isset($changes['password'])) {
                    $entity->setPassword(
                        hash($entity->getAlgorithm(), $entity->getSalt().strtolower($changes['password'][1]))
                    );
                }
            }
        }
    }
}
