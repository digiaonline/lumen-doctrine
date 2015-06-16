<?php namespace Nord\Lumen\Doctrine\EventListeners;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Jenssegers\Date\Date;
use Nord\Lumen\Doctrine\Traits\SoftDeletes;

class SoftDeletesListener
{

    /**
     * @var string
     */
    private $propertyName = 'deletedAt';


    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork    = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if ( ! $this->isSoftDeletable($entity)) {
                continue;
            }

            $metadata = $entityManager->getClassMetadata(get_class($entity));

            $oldValue = $metadata->getFieldValue($entity, $this->propertyName);

            if ($oldValue !== null) {
                continue;
            }

            $newValue = Date::now();

            $metadata->setFieldValue($entity, $this->propertyName, $newValue);

            $entityManager->persist($entity);

            $unitOfWork->propertyChanged($entity, $this->propertyName, $oldValue, $newValue);
            $unitOfWork->scheduleExtraUpdate($entity, [$this->propertyName => [$oldValue, $newValue]]);
        }
    }


    /**
     * @param mixed $entity
     *
     * @return bool
     */
    protected function isSoftDeletable($entity)
    {
        return array_key_exists(SoftDeletes::class, class_uses($entity));
    }
}
