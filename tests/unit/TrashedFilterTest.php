<?php

namespace Nord\Lumen\Doctrine\ORM\Test;

use \Nord\Lumen\Doctrine\ORM\Filters\TrashedFilter;
use \Doctrine\ORM\EntityManagerInterface;
use \Nord\Lumen\Doctrine\ORM\Traits\SoftDeletes;

class TrashedFilterTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;

    /** @var TrashedFilter */
    private $trashedFilter;

    /** @var EntityManagerInterface */
    private $entityManager;

    private $classMetadata;

    public function setup()
    {
        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->classMetadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->trashedFilter = new TrashedFilter($this->entityManager);
    }

    public function teardown()
    {
        $this->entityManager = null;
        $this->trashedFilter = null;
    }

    public function testAddFilterConstraintShouldReturnEmptyString()
    {
        $targetTableAlias = __DIR__;
        $this->classMetadata->rootEntityName = 'stdClass';

        $this->specify('verify returns correct query for filtering', function() use ($targetTableAlias) {
            verify($this->trashedFilter->addFilterConstraint($this->classMetadata, $targetTableAlias))->equals('');
        });
    }

    public function testAddFilterConstraintShouldReturnCorrectQuery()
    {
        $targetTableAlias = __DIR__;
        $this->classMetadata->rootEntityName = '\Nord\Lumen\Doctrine\ORM\Test\FooEntityClass';

        $this->specify('verify returns correct query', function() use ($targetTableAlias) {
            verify($this->trashedFilter->addFilterConstraint($this->classMetadata, $targetTableAlias))->equals("({$targetTableAlias}.deleted_at IS NULL OR {$targetTableAlias}.deleted_at > NOW())");
        });
    }
}