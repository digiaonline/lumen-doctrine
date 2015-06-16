<?php namespace Nord\Lumen\Doctrine;

use Doctrine\ORM\Mapping\NamingStrategy as NamingStrategyContract;

class NamingStrategy implements NamingStrategyContract
{

    /**
     * @inheritdoc
     */
    function classToTableName($className)
    {
        return str_plural($this->normalizeClassName($className));
    }


    /**
     * @inheritdoc
     */
    function propertyToColumnName($propertyName, $className = null)
    {
        return snake_case($propertyName);
    }


    /**
     * @inheritdoc
     */
    function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null)
    {
        return $propertyName . '_' . $embeddedColumnName;
    }


    /**
     * @inheritdoc
     */
    function referenceColumnName()
    {
        return 'id';
    }


    /**
     * @inheritdoc
     */
    function joinColumnName($propertyName)
    {
        return snake_case(str_singular($propertyName)) . '_' . $this->referenceColumnName();
    }


    /**
     * @inheritdoc
     */
    function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        return $this->normalizeClassName($sourceEntity) . '_' . $this->normalizeClassName($targetEntity);
    }


    /**
     * @inheritdoc
     */
    function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return $this->normalizeClassName($entityName) . '_' . ( $referencedColumnName ?: $this->referenceColumnName() );
    }


    /**
     * @param string $className
     *
     * @return string
     */
    private function normalizeClassName($className)
    {
        return snake_case(class_basename($className));
    }
}
