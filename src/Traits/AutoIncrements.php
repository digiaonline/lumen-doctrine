<?php namespace Nord\Lumen\Doctrine\ORM\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AutoIncrements
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     *
     * @var int
     */
    private $autoIncrementId;

    /**
     * @return int
     */
    public function getAutoIncrementId()
    {
        return $this->autoIncrementId;
    }
}
