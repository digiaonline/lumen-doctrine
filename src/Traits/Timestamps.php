<?php

namespace Nord\Lumen\Doctrine\ORM\Traits;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

trait Timestamps
{
    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var Carbon
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     *
     * @var Carbon
     */
    private $updatedAt;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = Carbon::now();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = Carbon::now();
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getCreatedAtTimestamp()
    {
        return $this->createdAt->getTimestamp();
    }

    /**
     * @return int|null
     */
    public function getUpdatedAtTimestamp()
    {
        return $this->updatedAt instanceof Carbon ? $this->updatedAt->getTimestamp() : null;
    }
}
