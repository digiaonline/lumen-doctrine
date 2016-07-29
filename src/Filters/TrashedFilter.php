<?php

namespace Nord\Lumen\Doctrine\ORM\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Nord\Lumen\Doctrine\ORM\Traits\SoftDeletes;

class TrashedFilter extends SQLFilter
{
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$this->isSoftDeletable($targetEntity->rootEntityName)) {
            return '';
        }

        return "({$targetTableAlias}.deleted_at IS NULL OR {$targetTableAlias}.deleted_at > NOW())";
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
