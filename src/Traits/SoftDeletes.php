<?php namespace Nord\Lumen\Doctrine\Traits;

use Doctrine\ORM\Mapping AS ORM;
use Jenssegers\Date\Date;

trait SoftDeletes
{

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     * @var Date
     */
    private $deletedAt;


    /**
     *
     */
    public function trash()
    {
        if ($this->isDeleted()) {
            return;
        }

        $this->deletedAt = Date::now();
    }


    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }


    /**
     * @return int|null
     */
    public function getDeletedAtTimestamp()
    {
        return $this->deletedAt instanceof Date ? $this->deletedAt->getTimestamp() : null;
    }


    /**
     * @return bool
     */
    private function isDeleted()
    {
        return $this->deletedAt === null;
    }
}
