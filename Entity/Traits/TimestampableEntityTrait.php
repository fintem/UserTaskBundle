<?php

namespace Fintem\UserTaskBundle\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Fintem\UserTaskBundle\Utils\Dates;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class TimestampableEntityTrait.
 */
trait TimestampableEntityTrait
{
    /**
     * @var \DateTimeImmutable
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;
    /**
     * @var \DateTimeImmutable
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * Returns createdAt
     *
     * @return DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return Dates::toImmuatable($this->createdAt);
    }

    /**
     * @param DateTimeImmutable $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     *
     * @return DateTimeImmutable
     */
    public function getUpdatedAt()
    {
        return Dates::toImmuatable($this->updatedAt);
    }

    /**
     * @param DateTimeImmutable $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
