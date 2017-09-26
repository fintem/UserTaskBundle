<?php

namespace Fintem\UserTaskBundle\Entity\Traits;

use Ramsey\Uuid\Uuid as UuidGenerator;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class UUIDTrait.
 *
 * @ORM\HasLifecycleCallbacks
 */
trait UUIDTrait
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @SE\Expose
     * @SE\Groups({"default"})
     * @SE\Type("string")
     */
    protected $itemId;

    public function __clone()
    {
        $this->itemId = null;
    }

    /**
     * @ORM\PrePersist()
     *
     * @return $this
     */
    public function createUUID()
    {
        if (null === $this->itemId) {
            $this->itemId = UuidGenerator::uuid1();
        }

        return $this;
    }

    /**
     * @internal Some bundles require getId method
     *
     * @return string
     */
    public function getId()
    {
        return $this->getItemId();
    }

    /**
     * @return string
     */
    public function getItemId()
    {
        if (null === $this->itemId) {
            $this->createUUID();
        }

        return $this->itemId;
    }

    /**
     * @return bool
     */
    public function isPersisted()
    {
        return null !== $this->itemId;
    }
}