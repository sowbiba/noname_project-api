<?php

namespace AppBundle\Exception;

use AppBundle\Utils\Implementer;

/**
 * Thrown to handle concurrency exceptions.
 */
class RevisionConflictException extends \Exception
{
    /**
     * @var object
     */
    private $currentEntity;

    /**
     * @var object
     */
    private $submittedEntity;

    /**
     * @var array
     */
    private $differences;

    /**
     * @param object $currentEntity
     * @param object $submittedEntity
     * @param array  $differences
     *
     * @throws \Exception
     */
    public function __construct($currentEntity, $submittedEntity, array $differences)
    {
        if (get_class($currentEntity) !== get_class($submittedEntity)) {
            throw new \Exception('The entities must have the same class.');
        }

        if (!Implementer::usesTrait($currentEntity, 'AppBundle\Model\Concurrency')) {
            throw new \Exception(
                sprintf(
                    'The entity "%s" must use "%s" trait.',
                    get_class($currentEntity),
                    'AppBundle\Model\Concurrency'
                )
            );
        }

        $this->currentEntity = $currentEntity;
        $this->submittedEntity = $submittedEntity;
        $this->differences = $differences;
    }

    /**
     * @return object
     */
    public function getCurrentEntity()
    {
        return $this->currentEntity;
    }

    /**
     * @return object
     */
    public function getSubmittedEntity()
    {
        return $this->submittedEntity;
    }

    /**
     * @return array
     */
    public function getDifferences()
    {
        return $this->differences;
    }
}
