<?php

namespace Nord\Lumen\Doctrine\ORM\Traits;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

trait SoftDeletes
{
    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     *
     * @var Carbon
     */
    private $deletedAt;


    public function trash()
    {
        if ($this->isDeleted()) {
            return;
        }

        $this->deletedAt = Carbon::now();
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
        return $this->deletedAt instanceof Carbon ? $this->deletedAt->getTimestamp() : null;
    }

    /**
     * @return bool
     */
    private function isDeleted()
    {
        return $this->deletedAt === null;
    }
}
